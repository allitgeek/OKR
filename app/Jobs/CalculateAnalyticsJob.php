<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\{User, Objective, PerformanceMetric, AnalyticsSnapshot};
use App\Services\AnalyticsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CalculateAnalyticsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Carbon $date;
    private string $period;

    public function __construct(Carbon $date = null, string $period = 'daily')
    {
        $this->date = $date ?: Carbon::now();
        $this->period = $period;
    }

    public function handle(AnalyticsService $analyticsService): void
    {
        Log::info("Starting analytics calculation for {$this->date->format('Y-m-d')} ({$this->period})");

        try {
            switch ($this->period) {
                case 'daily':
                    $this->calculateDailyMetrics($analyticsService);
                    break;
                case 'weekly':
                    $this->calculateWeeklyMetrics($analyticsService);
                    break;
                case 'monthly':
                    $this->calculateMonthlyMetrics($analyticsService);
                    break;
            }

            Log::info("Completed analytics calculation for {$this->date->format('Y-m-d')} ({$this->period})");
        } catch (\Exception $e) {
            Log::error("Analytics calculation failed: {$e->getMessage()}");
            throw $e;
        }
    }

    private function calculateDailyMetrics(AnalyticsService $analyticsService): void
    {
        $today = $this->date->startOfDay();
        $tomorrow = $this->date->copy()->addDay()->startOfDay();

        // Company metrics
        $objectivesCreated = Objective::whereBetween('created_at', [$today, $tomorrow])->count();
        AnalyticsSnapshot::recordMetric($this->date->toDateString(), 'objectives_created', $objectivesCreated, 'company');

        $objectivesCompleted = Objective::where('status', 'completed')
            ->whereBetween('updated_at', [$today, $tomorrow])->count();
        AnalyticsSnapshot::recordMetric($this->date->toDateString(), 'objectives_completed', $objectivesCompleted, 'company');

        // Success rate
        $totalObjectives = Objective::count();
        $completedObjectives = Objective::where('status', 'completed')->count();
        $successRate = $totalObjectives > 0 ? ($completedObjectives / $totalObjectives) * 100 : 0;
        AnalyticsSnapshot::recordMetric($this->date->toDateString(), 'success_rate', $successRate, 'company');
    }

    private function calculateWeeklyMetrics(AnalyticsService $analyticsService): void
    {
        $startOfWeek = $this->date->copy()->startOfWeek();
        $endOfWeek = $this->date->copy()->endOfWeek();

        $users = User::all();
        foreach ($users as $user) {
            PerformanceMetric::calculateForUser($user->id, $startOfWeek, $endOfWeek);
        }
    }

    private function calculateMonthlyMetrics(AnalyticsService $analyticsService): void
    {
        $startOfMonth = $this->date->copy()->startOfMonth();
        $endOfMonth = $this->date->copy()->endOfMonth();

        // Monthly success rate
        $objectives = Objective::whereBetween('created_at', [$startOfMonth, $endOfMonth])->get();
        $completed = $objectives->where('status', 'completed')->count();
        $total = $objectives->count();
        $successRate = $total > 0 ? ($completed / $total) * 100 : 0;

        AnalyticsSnapshot::recordMetric(
            $endOfMonth->toDateString(),
            'monthly_success_rate',
            $successRate,
            'company',
            null,
            [
                'period_start' => $startOfMonth->format('Y-m-d'),
                'period_end' => $endOfMonth->format('Y-m-d'),
                'total_objectives' => $total,
                'completed_objectives' => $completed
            ]
        );
    }
} 