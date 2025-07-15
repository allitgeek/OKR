<?php

namespace App\Console\Commands;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Console\Command;

class EscalateOverdueTasks extends Command
{
    protected $signature = 'tasks:escalate-overdue';
    protected $description = 'Escalate tasks that have been pending acceptance for too long';

    public function handle(): void
    {
        $this->info('Starting task escalation process...');

        $tasks = Task::where('status', 'assigned')
            ->where('created_at', '<=', Carbon::now()->subDays(2))
            ->get();

        foreach ($tasks as $task) {
            $task->update(['status' => 'pending_acceptance']);
            
            // Notify creator and assignee
            $task->creator->notify(new TaskEscalated($task));
            $task->assignee->notify(new TaskEscalated($task));
        }

        $this->info("Escalated {$tasks->count()} tasks.");
    }
} 