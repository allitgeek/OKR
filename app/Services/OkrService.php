<?php

namespace App\Services;

use App\Models\{Objective, KeyResult, OkrCycle, OkrCheckIn, User};
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OkrService
{
    /**
     * Get or create the current OKR cycle
     */
    public function getCurrentCycle(): ?OkrCycle
    {
        try {
            // First try to get current cycle by date range
            $current = OkrCycle::getCurrent();
            
            // If no current cycle by date, try to get active cycle
            if (!$current) {
                $current = OkrCycle::active()->first();
            }
            
            // If still no cycle, create one for current quarter
            if (!$current) {
                $now = Carbon::now();
                $quarter = $now->quarter;
                $year = $now->year;
                
                $current = $this->createCycle($year, $quarter);
            }
            
            return $current;
        } catch (\Exception $e) {
            Log::error('Get Current Cycle Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a new OKR cycle
     */
    public function createCycle(int $year, int $quarter): OkrCycle
    {
        $startDate = Carbon::createFromDate($year, ($quarter - 1) * 3 + 1, 1);
        $endDate = $startDate->copy()->addMonths(3)->subDay();
        
        return OkrCycle::create([
            'name' => "Q{$quarter}-{$year}",
            'year' => $year,
            'quarter' => $quarter,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'planning_start' => $startDate->copy()->subWeeks(2),
            'mid_quarter_review' => $startDate->copy()->addDays(45),
            'scoring_deadline' => $endDate->copy()->addWeeks(1),
            'status' => 'planning',
            'description' => "Quarter {$quarter} {$year} OKR Cycle"
        ]);
    }

    /**
     * Initialize OKR cycles for the current year
     */
    public function initializeYear(int $year = null): Collection
    {
        $year = $year ?? Carbon::now()->year;
        
        // Check if cycles already exist for this year
        if (OkrCycle::forYear($year)->exists()) {
            return OkrCycle::forYear($year)->get();
        }
        
        OkrCycle::createQuarterlySchedule($year);
        
        return OkrCycle::forYear($year)->get();
    }

    /**
     * Start a new OKR cycle
     */
    public function startCycle(OkrCycle $cycle): bool
    {
        // Close previous cycle if active
        $currentActive = OkrCycle::active()->where('id', '!=', $cycle->id)->first();
        if ($currentActive) {
            $this->closeCycle($currentActive);
        }
        
        $cycle->update(['status' => 'active']);
        
        // Set current cycle for all objectives without a cycle
        Objective::whereNull('cycle_id')->update([
            'cycle_id' => $cycle->name,
            'cycle_year' => $cycle->year,
            'cycle_quarter' => $cycle->quarter
        ]);
        
        return true;
    }

    /**
     * Close an OKR cycle and calculate final scores
     */
    public function closeCycle(OkrCycle $cycle): bool
    {
        // Calculate final scores for all objectives in this cycle
        $objectives = $cycle->objectives()->get();
        
        foreach ($objectives as $objective) {
            $this->scoreObjective($objective);
        }
        
        $cycle->update(['status' => 'closed']);
        
        return true;
    }

    /**
     * Calculate the final OKR score for an objective
     */
    public function scoreObjective(Objective $objective): float
    {
        // Calculate scores for all key results first
        foreach ($objective->keyResults as $keyResult) {
            $this->scoreKeyResult($keyResult);
        }
        
        // Calculate objective score as average of key result scores
        return $objective->calculateOkrScore();
    }

    /**
     * Calculate the final OKR score for a key result
     */
    public function scoreKeyResult(KeyResult $keyResult): float
    {
        return $keyResult->calculateOkrScore();
    }

    /**
     * Get health report for a specific cycle
     */
    public function getCycleHealthReport(OkrCycle $cycle): array
    {
        $objectives = $cycle->objectives()->with('keyResults')->get();
        
        if ($objectives->isEmpty()) {
            return [
                'total_objectives' => 0,
                'successful_objectives' => 0,
                'success_rate' => 0,
                'average_score' => 0,
                'average_confidence' => 0,
                'needs_attention' => 0,
                'distribution' => ['committed' => 0, 'aspirational' => 0],
                'levels' => ['individual' => 0, 'team' => 0, 'company' => 0],
                'top_performers' => [],
                'at_risk' => []
            ];
        }

        $totalObjectives = $objectives->count();
        $successfulObjectives = $objectives->filter(fn($obj) => $obj->isSuccessful())->count();
        
        $averageScore = $objectives->avg('okr_score') ?? 0;
        $averageConfidence = $objectives->avg('confidence_level') ?? 0.5;
        
        $needsAttention = $objectives->filter(fn($obj) => $obj->needsAttention())->count();
        
        $distribution = [
            'committed' => $objectives->where('okr_type', 'committed')->count(),
            'aspirational' => $objectives->where('okr_type', 'aspirational')->count()
        ];
        
        $levels = [
            'individual' => $objectives->where('level', 'individual')->count(),
            'team' => $objectives->where('level', 'team')->count(),
            'company' => $objectives->where('level', 'company')->count()
        ];

        $topPerformers = $objectives->sortByDesc('okr_score')->take(5);
        $atRisk = $objectives->filter(fn($obj) => $obj->needsAttention())->sortBy('okr_score');

        return [
            'total_objectives' => $totalObjectives,
            'successful_objectives' => $successfulObjectives,
            'success_rate' => $totalObjectives > 0 ? round(($successfulObjectives / $totalObjectives) * 100, 1) : 0,
            'average_score' => round($averageScore, 2),
            'average_confidence' => round($averageConfidence, 2),
            'needs_attention' => $needsAttention,
            'distribution' => $distribution,
            'levels' => $levels,
            'top_performers' => $topPerformers,
            'at_risk' => $atRisk
        ];
    }

    /**
     * Get overall health report across all cycles
     */
    public function getOverallHealthReport(): array
    {
        try {
            $currentCycle = $this->getCurrentCycle();
            
            if (!$currentCycle) {
                // Return default values if no current cycle
                return [
                    'total_objectives' => 0,
                    'successful_objectives' => 0,
                    'success_rate' => 0,
                    'average_score' => 0,
                    'average_confidence' => 0.5,
                    'needs_attention' => 0,
                    'distribution' => ['committed' => 0, 'aspirational' => 0],
                    'levels' => ['individual' => 0, 'team' => 0, 'company' => 0],
                    'top_performers' => [],
                    'at_risk' => [],
                    'recent_check_ins' => 0,
                    'total_all_time_objectives' => 0,
                    'current_cycle' => null
                ];
            }
            
            $cycleReport = $this->getCycleHealthReport($currentCycle);
            
            // Add some additional overall metrics
            $allObjectives = Objective::with('keyResults')->get();
            $recentCheckIns = OkrCheckIn::recent(30)->count();
            
            $cycleReport['recent_check_ins'] = $recentCheckIns;
            $cycleReport['total_all_time_objectives'] = $allObjectives->count();
            $cycleReport['current_cycle'] = $currentCycle;
            
            return $cycleReport;
        } catch (\Exception $e) {
            Log::error('Health Report Error: ' . $e->getMessage());
            
            // Return safe defaults
            return [
                'total_objectives' => 0,
                'successful_objectives' => 0,
                'success_rate' => 0,
                'average_score' => 0,
                'average_confidence' => 0.5,
                'needs_attention' => 0,
                'distribution' => ['committed' => 0, 'aspirational' => 0],
                'levels' => ['individual' => 0, 'team' => 0, 'company' => 0],
                'top_performers' => [],
                'at_risk' => [],
                'recent_check_ins' => 0,
                'total_all_time_objectives' => 0,
                'current_cycle' => null
            ];
        }
    }



    /**
     * Validate OKR quality
     */
    public function validateOkrQuality(Objective $objective): array
    {
        $issues = [];
        
        // Check if objective is specific and measurable
        if (!$objective->is_specific) {
            $issues[] = 'Objective should be more specific';
        }
        
        if (!$objective->is_measurable) {
            $issues[] = 'Objective should be measurable';
        }
        
        // Check key results
        foreach ($objective->keyResults as $keyResult) {
            if (!$keyResult->is_measurable) {
                $issues[] = "Key Result '{$keyResult->title}' should be measurable";
            }
            
            if (!$keyResult->is_time_bound) {
                $issues[] = "Key Result '{$keyResult->title}' should be time-bound";
            }
            
            if ($keyResult->target_value <= 0) {
                $issues[] = "Key Result '{$keyResult->title}' should have a positive target value";
            }
        }
        
        return $issues;
    }

    /**
     * Create alignment between objectives
     */
    public function alignObjective(Objective $child, Objective $parent): bool
    {
        // Validate alignment is possible
        if ($child->id === $parent->id) {
            return false; // Can't align to self
        }
        
        if ($this->wouldCreateCircularAlignment($child, $parent)) {
            return false; // Would create circular dependency
        }
        
        $child->update(['parent_objective_id' => $parent->id]);
        
        return true;
    }

    /**
     * Check if alignment would create circular dependency
     */
    private function wouldCreateCircularAlignment(Objective $child, Objective $parent): bool
    {
        $current = $parent;
        
        while ($current && $current->parent_objective_id) {
            if ($current->parent_objective_id === $child->id) {
                return true;
            }
            $current = $current->parentObjective;
        }
        
        return false;
    }

    /**
     * Get objectives hierarchy for alignment visualization
     */
    public function getObjectiveHierarchy(string $cycleId = null): Collection
    {
        $query = Objective::with(['childObjectives', 'keyResults'])
                          ->whereNull('parent_objective_id'); // Top-level objectives
        
        if ($cycleId) {
            $query->forCycle($cycleId);
        }
        
        return $query->get();
    }

    /**
     * Generate OKR health report
     */
    public function generateHealthReport(string $cycleId = null): array
    {
        $cycle = $cycleId ? OkrCycle::where('name', $cycleId)->first() : $this->getCurrentCycle();
        
        $objectives = Objective::forCycle($cycle->name)->with(['keyResults'])->get();
        
        $stats = [
            'total_objectives' => $objectives->count(),
            'successful_objectives' => $objectives->filter->isSuccessful()->count(),
            'average_confidence' => $objectives->avg('confidence_level') ?? 0,
            'average_score' => $objectives->avg('okr_score') ?? 0,
            'needs_attention' => $objectives->filter->needsAttention()->count(),
            'by_type' => [
                'committed' => $objectives->where('okr_type', 'committed')->count(),
                'aspirational' => $objectives->where('okr_type', 'aspirational')->count(),
            ],
            'by_level' => [
                'company' => $objectives->where('level', 'company')->count(),
                'team' => $objectives->where('level', 'team')->count(),
                'individual' => $objectives->where('level', 'individual')->count(),
            ]
        ];
        
        return [
            'cycle' => $cycle,
            'stats' => $stats,
            'recommendations' => $this->generateRecommendations($stats)
        ];
    }

    /**
     * Generate recommendations based on OKR health
     */
    private function generateRecommendations(array $stats): array
    {
        $recommendations = [];
        
        if ($stats['average_confidence'] < 0.5) {
            $recommendations[] = 'Overall confidence is low. Consider reviewing objective difficulty and resource allocation.';
        }
        
        if ($stats['average_score'] < 0.5) {
            $recommendations[] = 'Average OKR scores are below expectations. Review progress and consider adjusting targets.';
        }
        
        if ($stats['needs_attention'] > $stats['total_objectives'] * 0.3) {
            $recommendations[] = 'More than 30% of OKRs need attention. Schedule team check-ins and provide additional support.';
        }
        
        if ($stats['by_type']['aspirational'] === 0) {
            $recommendations[] = 'Consider adding aspirational OKRs to drive innovation and stretch goals.';
        }
        
        return $recommendations;
    }
} 