<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\CalculateAnalyticsJob;
use Carbon\Carbon;

class CalculateAnalytics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:calculate {period=daily : The period to calculate (daily, weekly, monthly)} {--date= : Specific date to calculate (Y-m-d format)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate analytics metrics for specified period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $period = $this->argument('period');
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::now();

        $this->info("Starting analytics calculation for {$date->format('Y-m-d')} ({$period})");

        try {
            // Dispatch the analytics calculation job
            CalculateAnalyticsJob::dispatch($date, $period);

            $this->info("Analytics calculation job dispatched successfully!");
            $this->line("Period: {$period}");
            $this->line("Date: {$date->format('Y-m-d')}");
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to dispatch analytics calculation job: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}
