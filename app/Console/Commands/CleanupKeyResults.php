<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\KeyResult;

class CleanupKeyResults extends Command
{
    protected $signature = 'keyresults:cleanup';
    protected $description = 'Delete duplicate key results';

    public function handle()
    {
        $count = KeyResult::where('title', 'Key result2')->delete();
        $this->info("Deleted {$count} duplicate key results.");
    }
} 