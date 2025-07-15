<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    protected Task $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Task Assigned')
            ->line('You have been assigned a new task:')
            ->line('Title: ' . $this->task->title)
            ->line('Priority: ' . ucfirst($this->task->priority))
            ->line('Due Date: ' . $this->task->due_date->format('M d, Y'))
            ->action('View Task', url('/tasks/' . $this->task->id))
            ->line('Please review and accept/reject the task.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'priority' => $this->task->priority,
            'due_date' => $this->task->due_date->format('Y-m-d'),
            'creator_name' => $this->task->creator->name,
        ];
    }
} 