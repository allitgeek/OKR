<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\EscalateOverdueTasks::class,
        Commands\MakeSuperAdmin::class,
        Commands\CalculateAnalytics::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('tasks:escalate-overdue')->daily();
        
        // Analytics calculations
        $schedule->command('analytics:calculate daily')->daily()->at('02:00');
        $schedule->command('analytics:calculate weekly')->weekly()->mondays()->at('03:00');
        $schedule->command('analytics:calculate monthly')->monthly()->at('04:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
} 