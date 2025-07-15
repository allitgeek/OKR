<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Objective;

class RecalculateObjectiveProgress extends Command
{
    protected $signature = 'objectives:recalculate-progress';
    protected $description = 'Recalculate progress for all objectives';

    public function handle()
    {
        $objectives = Objective::all();
        foreach ($objectives as $objective) {
            $objective->calculateProgress();
        }
        $this->info('Progress recalculated for all objectives.');
    }
} 