<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OkrService;
use App\Models\OkrCycle;
use Carbon\Carbon;

class InitializeOkrCycle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'okr:init-cycle 
                           {year? : The year to initialize (defaults to current year)}
                           {--start-current : Start the current quarter cycle}
                           {--force : Force recreation if cycles already exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize OKR cycles for a given year and optionally start the current quarter';

    protected OkrService $okrService;

    public function __construct(OkrService $okrService)
    {
        parent::__construct();
        $this->okrService = $okrService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $year = $this->argument('year') ?: Carbon::now()->year;
        $force = $this->option('force');
        $startCurrent = $this->option('start-current');

        $this->info("Initializing OKR cycles for year {$year}...");

        // Check if cycles already exist
        $existingCycles = OkrCycle::forYear($year)->get();
        
        if ($existingCycles->isNotEmpty() && !$force) {
            $this->warn("OKR cycles for {$year} already exist:");
            
            $headers = ['Name', 'Quarter', 'Start Date', 'End Date', 'Status'];
            $rows = $existingCycles->map(function ($cycle) {
                return [
                    $cycle->name,
                    "Q{$cycle->quarter}",
                    $cycle->start_date->format('Y-m-d'),
                    $cycle->end_date->format('Y-m-d'),
                    $cycle->status
                ];
            })->toArray();
            
            $this->table($headers, $rows);
            
            if (!$this->confirm('Do you want to continue anyway? (use --force to skip this check)')) {
                return;
            }
        }

        try {
            // Initialize the cycles
            if ($force && $existingCycles->isNotEmpty()) {
                $this->info('Deleting existing cycles...');
                OkrCycle::forYear($year)->delete();
            }

            $cycles = $this->okrService->initializeYear($year);
            
            $this->info("Successfully created {$cycles->count()} OKR cycles for {$year}:");
            
            $headers = ['Name', 'Quarter', 'Start Date', 'End Date', 'Status'];
            $rows = $cycles->map(function ($cycle) {
                return [
                    $cycle->name,
                    "Q{$cycle->quarter}",
                    $cycle->start_date->format('Y-m-d'),
                    $cycle->end_date->format('Y-m-d'),
                    $cycle->status
                ];
            })->toArray();
            
            $this->table($headers, $rows);

            // Start current quarter if requested
            if ($startCurrent) {
                $currentQuarter = Carbon::now()->quarter;
                $currentCycle = $cycles->where('quarter', $currentQuarter)->first();
                
                if ($currentCycle) {
                    $this->info("Starting current quarter cycle: {$currentCycle->name}");
                    $this->okrService->startCycle($currentCycle);
                    $this->info("✅ {$currentCycle->name} is now active!");
                } else {
                    $this->warn("Could not find cycle for current quarter Q{$currentQuarter}");
                }
            }

            // Show next steps
            $this->showNextSteps($year);

        } catch (\Exception $e) {
            $this->error("Failed to initialize OKR cycles: {$e->getMessage()}");
            return 1;
        }

        return 0;
    }

    private function showNextSteps(int $year): void
    {
        $this->newLine();
        $this->info('🎯 Next Steps:');
        $this->line('1. Review the created cycles in your OKR dashboard');
        $this->line('2. Start the appropriate cycle when ready: php artisan okr:start-cycle Q1-' . $year);
        $this->line('3. Begin setting objectives for the active cycle');
        $this->line('4. Set up regular check-ins and reviews');
        
        $currentCycle = OkrCycle::getCurrent();
        if ($currentCycle) {
            $this->newLine();
            $this->info("📊 Current Active Cycle: {$currentCycle->name}");
            $this->line("   Status: {$currentCycle->status}");
            $this->line("   Progress: {$currentCycle->getProgressPercentage()}%");
            $this->line("   Days Remaining: {$currentCycle->getDaysRemaining()}");
        }
    }
} 