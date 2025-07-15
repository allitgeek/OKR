<?php

namespace App\Services;

use App\Models\{User, Objective, PerformanceMetric, AnalyticsSnapshot};
use Carbon\Carbon;
use Illuminate\Support\Facades\{DB, Cache};

class AnalyticsService
{
    private const CACHE_TTL = 3600; // 1 hour

    /**
     * Generate success rate metrics for a given period
     */
    public function generateSuccessRateMetrics($startDate = null, $endDate = null)
    {
        $startDate = $startDate ?: Carbon::now()->startOfQuarter();
        $endDate = $endDate ?: Carbon::now()->endOfQuarter();

        $cacheKey = "success_rate_metrics_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function() use ($startDate, $endDate) {
            // Company-wide metrics
            $companyMetrics = $this->calculateCompanyMetrics($startDate, $endDate);
            
            // Department-wise metrics
            $departmentMetrics = $this->calculateDepartmentMetrics($startDate, $endDate);
            
            // Individual user metrics
            $userMetrics = $this->calculateUserMetrics($startDate, $endDate);

            return [
                'period' => [
                    'start' => $startDate->format('Y-m-d'),
                    'end' => $endDate->format('Y-m-d'),
                ],
                'company' => $companyMetrics,
                'departments' => $departmentMetrics,
                'top_performers' => $userMetrics->sortByDesc('success_rate')->take(10),
                'needs_attention' => $userMetrics->where('success_rate', '<', 70)->sortBy('success_rate'),
            ];
        });
    }

    /**
     * Calculate team performance heatmap data
     */
    public function calculateTeamPerformance($teamIdentifier = null)
    {
        $cacheKey = "team_performance_" . ($teamIdentifier ?: 'all');
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function() use ($teamIdentifier) {
            $query = User::with(['objectives' => function($q) {
                $q->whereBetween('created_at', [Carbon::now()->startOfQuarter(), Carbon::now()]);
            }]);

            if ($teamIdentifier) {
                // Assuming you have a department field or similar
                $query->where('department', $teamIdentifier);
            }

            $users = $query->get();

            return $users->map(function($user) {
                $objectives = $user->objectives;
                $completed = $objectives->where('status', 'completed')->count();
                $total = $objectives->count();
                $successRate = $total > 0 ? ($completed / $total) * 100 : 0;

                return [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'department' => $user->department ?? 'Unassigned',
                    'total_objectives' => $total,
                    'completed_objectives' => $completed,
                    'success_rate' => round($successRate, 2),
                    'status_color' => $this->getPerformanceColor($successRate),
                    'last_activity' => $user->objectives->max('updated_at'),
                ];
            })->groupBy('department');
        });
    }

    /**
     * Get predictive insights for objectives
     */
    public function getPredictiveInsights($objectiveId = null)
    {
        $objectives = $objectiveId 
            ? Objective::where('id', $objectiveId)->get()
            : Objective::where('status', '!=', 'completed')->get();

        return $objectives->map(function($objective) {
            $riskFactors = $this->analyzeRiskFactors($objective);
            $completionProbability = $this->calculateCompletionProbability($objective, $riskFactors);
            
            return [
                'objective_id' => $objective->id,
                'title' => $objective->title,
                'completion_probability' => $completionProbability,
                'risk_level' => $this->getRiskLevel($completionProbability),
                'risk_factors' => $riskFactors,
                'recommendations' => $this->generateRecommendations($objective, $riskFactors),
            ];
        });
    }

    /**
     * Generate custom report based on configuration
     */
    public function generateCustomReport($config)
    {
        $reportData = [];

        foreach ($config['components'] as $component) {
            switch ($component['type']) {
                case 'success_rate_chart':
                    $reportData[$component['id']] = $this->getSuccessRateChartData($component['parameters']);
                    break;
                case 'team_comparison':
                    $reportData[$component['id']] = $this->getTeamComparisonData($component['parameters']);
                    break;
                case 'objective_timeline':
                    $reportData[$component['id']] = $this->getObjectiveTimelineData($component['parameters']);
                    break;
                case 'performance_table':
                    $reportData[$component['id']] = $this->getPerformanceTableData($component['parameters']);
                    break;
            }
        }

        return [
            'report_id' => uniqid('report_'),
            'generated_at' => Carbon::now(),
            'config' => $config,
            'data' => $reportData,
        ];
    }

    /**
     * Get trending data for dashboard charts
     */
    public function getTrendingData($metricType, $period = 30)
    {
        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subDays($period);

        return AnalyticsSnapshot::ofType($metricType)
            ->forPeriod($startDate, $endDate)
            ->orderBy('snapshot_date')
            ->get()
            ->groupBy('entity_type')
            ->map(function($snapshots) {
                return $snapshots->map(function($snapshot) {
                    return [
                        'date' => $snapshot->snapshot_date->format('Y-m-d'),
                        'value' => $snapshot->metric_value,
                        'metadata' => $snapshot->metadata,
                    ];
                });
            });
    }

    /**
     * Calculate real-time dashboard metrics
     */
    public function getDashboardMetrics()
    {
        $cacheKey = 'dashboard_metrics_' . Carbon::now()->format('Y-m-d-H');
        
        return Cache::remember($cacheKey, 300, function() { // 5 minutes cache
            $currentQuarter = [
                Carbon::now()->startOfQuarter(),
                Carbon::now()->endOfQuarter()
            ];

            $totalObjectives = Objective::whereBetween('created_at', $currentQuarter)->count();
            $completedObjectives = Objective::whereBetween('created_at', $currentQuarter)
                ->where('status', 'completed')->count();
            $overdueObjectives = Objective::where('due_date', '<', Carbon::now())
                ->where('status', '!=', 'completed')->count();
            $activeUsers = User::whereHas('objectives', function($q) use ($currentQuarter) {
                $q->whereBetween('created_at', $currentQuarter);
            })->count();

            $successRate = $totalObjectives > 0 ? ($completedObjectives / $totalObjectives) * 100 : 0;

            // Get previous quarter for comparison
            $previousQuarter = [
                Carbon::now()->subQuarter()->startOfQuarter(),
                Carbon::now()->subQuarter()->endOfQuarter()
            ];
            $previousSuccessRate = $this->getSuccessRateForPeriod($previousQuarter[0], $previousQuarter[1]);
            $successRateTrend = $successRate - $previousSuccessRate;

            return [
                'success_rate' => round($successRate, 1),
                'success_rate_trend' => round($successRateTrend, 1),
                'total_objectives' => $totalObjectives,
                'completed_objectives' => $completedObjectives,
                'overdue_objectives' => $overdueObjectives,
                'active_users' => $activeUsers,
                'objectives_due_today' => Objective::whereDate('due_date', Carbon::today())
                    ->where('status', '!=', 'completed')->count(),
                'at_risk_count' => $this->getAtRiskObjectivesCount(),
            ];
        });
    }

    // Private helper methods

    private function calculateCompanyMetrics($startDate, $endDate)
    {
        $objectives = Objective::whereBetween('created_at', [$startDate, $endDate])->get();
        $completed = $objectives->where('status', 'completed')->count();
        $total = $objectives->count();

        return [
            'total_objectives' => $total,
            'completed_objectives' => $completed,
            'success_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
            'avg_completion_time' => $this->calculateAverageCompletionTime($objectives->where('status', 'completed')),
        ];
    }

    private function calculateDepartmentMetrics($startDate, $endDate)
    {
        return User::select('department')
            ->whereNotNull('department')
            ->distinct()
            ->get()
            ->map(function($dept) use ($startDate, $endDate) {
                $users = User::where('department', $dept->department)->pluck('id');
                $objectives = Objective::whereIn('user_id', $users)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get();

                $completed = $objectives->where('status', 'completed')->count();
                $total = $objectives->count();

                return [
                    'department' => $dept->department,
                    'total_objectives' => $total,
                    'completed_objectives' => $completed,
                    'success_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
                    'team_size' => $users->count(),
                ];
            });
    }

    private function calculateUserMetrics($startDate, $endDate)
    {
        return User::with(['objectives' => function($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        }])->get()->map(function($user) {
            $objectives = $user->objectives;
            $completed = $objectives->where('status', 'completed')->count();
            $total = $objectives->count();

            return [
                'user_id' => $user->id,
                'name' => $user->name,
                'total_objectives' => $total,
                'completed_objectives' => $completed,
                'success_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
            ];
        });
    }

    private function analyzeRiskFactors($objective)
    {
        $factors = [];
        $now = Carbon::now();

        // Time-based risk factors
        if ($objective->due_date) {
            $daysRemaining = $now->diffInDays($objective->due_date, false);
            if ($daysRemaining < 0) {
                $factors[] = 'overdue';
            } elseif ($daysRemaining < 7) {
                $factors[] = 'due_soon';
            }
        }

        // Progress-based risk factors
        $keyResults = $objective->keyResults ?? collect();
        if ($keyResults->count() > 0) {
            $avgProgress = $keyResults->avg('progress');
            if ($avgProgress < 25) {
                $factors[] = 'low_progress';
            }
        }

        // Engagement risk factors
        $recentActivity = $objective->updated_at->diffInDays($now);
        if ($recentActivity > 7) {
            $factors[] = 'no_recent_activity';
        }

        return $factors;
    }

    private function calculateCompletionProbability($objective, $riskFactors)
    {
        $baseProbability = 75; // Start with 75% chance
        
        // Adjust based on risk factors
        foreach ($riskFactors as $factor) {
            switch ($factor) {
                case 'overdue':
                    $baseProbability -= 40;
                    break;
                case 'due_soon':
                    $baseProbability -= 15;
                    break;
                case 'low_progress':
                    $baseProbability -= 25;
                    break;
                case 'no_recent_activity':
                    $baseProbability -= 10;
                    break;
            }
        }

        return max(0, min(100, $baseProbability));
    }

    private function getRiskLevel($probability)
    {
        if ($probability >= 70) return 'low';
        if ($probability >= 40) return 'medium';
        return 'high';
    }

    private function generateRecommendations($objective, $riskFactors)
    {
        $recommendations = [];
        
        if (in_array('overdue', $riskFactors)) {
            $recommendations[] = 'Consider extending deadline or breaking into smaller tasks';
        }
        if (in_array('low_progress', $riskFactors)) {
            $recommendations[] = 'Schedule check-in meeting to identify blockers';
        }
        if (in_array('no_recent_activity', $riskFactors)) {
            $recommendations[] = 'Send reminder to update progress';
        }

        return $recommendations;
    }

    private function getPerformanceColor($successRate)
    {
        if ($successRate >= 90) return 'green';
        if ($successRate >= 70) return 'yellow';
        return 'red';
    }

    private function getSuccessRateForPeriod($startDate, $endDate)
    {
        $objectives = Objective::whereBetween('created_at', [$startDate, $endDate])->get();
        $completed = $objectives->where('status', 'completed')->count();
        $total = $objectives->count();
        
        return $total > 0 ? ($completed / $total) * 100 : 0;
    }

    private function getAtRiskObjectivesCount()
    {
        return Objective::where('status', '!=', 'completed')
            ->where(function($q) {
                $q->where('due_date', '<', Carbon::now()->addDays(7))
                  ->orWhereHas('keyResults', function($kr) {
                      $kr->where('progress', '<', 25);
                  });
            })->count();
    }

    private function calculateAverageCompletionTime($completedObjectives)
    {
        if ($completedObjectives->isEmpty()) return null;
        
        return $completedObjectives->avg(function($objective) {
            return $objective->created_at->diffInDays($objective->updated_at);
        });
    }
} 