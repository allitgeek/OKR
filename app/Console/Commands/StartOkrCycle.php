<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OkrService;
use App\Models\OkrCycle;

class StartOkrCycle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'okr:start-cycle 
                           {cycle : The cycle name to start (e.g., Q1-2025)}
                           {--force : Force start even if another cycle is active}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start a specific OKR cycle';

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
        $cycleName = $this->argument('cycle');
        $force = $this->option('force');

        // Find the cycle
        $cycle = OkrCycle::where('name', $cycleName)->first();
        
        if (!$cycle) {
            $this->error("Cycle '{$cycleName}' not found.");
            
            // Show available cycles
            $availableCycles = OkrCycle::all();
            if ($availableCycles->isNotEmpty()) {
                $this->info('Available cycles:');
                foreach ($availableCycles as $c) {
                    $this->line("  - {$c->name} ({$c->status})");
                }
            }
            
            return 1;
        }

        // Check if cycle is already active
        if ($cycle->status === 'active') {
            $this->warn("Cycle '{$cycleName}' is already active.");
            return 0;
        }

        // Check for existing active cycle
        $activeCycle = OkrCycle::active()->first();
        if ($activeCycle && !$force) {
            $this->warn("Another cycle is currently active: {$activeCycle->name}");
            
            if (!$this->confirm("Do you want to close '{$activeCycle->name}' and start '{$cycleName}'?")) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        try {
            $this->info("Starting OKR cycle: {$cycleName}...");
            
            if ($this->okrService->startCycle($cycle)) {
                $this->info("✅ Successfully started cycle: {$cycleName}");
                
                // Show cycle details
                $this->showCycleDetails($cycle);
                
                // Show recommendations
                $this->showRecommendations($cycle);
                
            } else {
                $this->error("Failed to start cycle: {$cycleName}");
                return 1;
            }

        } catch (\Exception $e) {
            $this->error("Error starting cycle: {$e->getMessage()}");
            return 1;
        }

        return 0;
    }

    private function showCycleDetails(OkrCycle $cycle): void
    {
        $this->newLine();
        $this->info("📊 Cycle Details:");
        $this->line("Name: {$cycle->name}");
        $this->line("Period: {$cycle->start_date->format('M j, Y')} - {$cycle->end_date->format('M j, Y')}");
        $this->line("Status: {$cycle->status}");
        $this->line("Days Remaining: {$cycle->getDaysRemaining()}");
        $this->line("Progress: {$cycle->getProgressPercentage()}%");
        
        if ($cycle->mid_quarter_review) {
            $this->line("Mid-Quarter Review: {$cycle->mid_quarter_review->format('M j, Y')}");
        }
        
        if ($cycle->scoring_deadline) {
            $this->line("Scoring Deadline: {$cycle->scoring_deadline->format('M j, Y')}");
        }
    }

    private function showRecommendations(OkrCycle $cycle): void
    {
        $this->newLine();
        $this->info("🎯 Recommendations:");
        
        // Check objectives count
        $objectivesCount = $cycle->objectives()->count();
        
        if ($objectivesCount === 0) {
            $this->line("• Start creating objectives for this cycle");
            $this->line("• Aim for 3-5 objectives per individual/team");
            $this->line("• Each objective should have 2-5 key results");
        } else {
            $this->line("• {$objectivesCount} objectives found in this cycle");
            $this->line("• Review objective alignment and cascading");
            $this->line("• Set up regular check-in schedule");
        }
        
        $this->line("• Schedule kick-off meeting with stakeholders");
        $this->line("• Communicate cycle timeline to all participants");
        $this->line("• Set up tracking and monitoring processes");
    }
} 