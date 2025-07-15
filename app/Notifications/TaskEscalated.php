<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskEscalated extends Notification implements ShouldQueue
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
        $isCreator = $notifiable->id === $this->task->creator_id;
        
        return (new MailMessage)
            ->subject('Task Requires Attention')
            ->line($isCreator 
                ? 'A task you created has been pending acceptance for too long:'
                : 'You have a task pending acceptance that requires immediate attention:'
            )
            ->line('Title: ' . $this->task->title)
            ->line('Priority: ' . ucfirst($this->task->priority))
            ->line('Due Date: ' . $this->task->due_date->format('M d, Y'))
            ->action('View Task', url('/tasks/' . $this->task->id))
            ->line($isCreator
                ? 'Please follow up with the assignee.'
                : 'Please accept or reject this task as soon as possible.'
            );
    }

    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'priority' => $this->task->priority,
            'due_date' => $this->task->due_date->format('Y-m-d'),
            'creator_name' => $this->task->creator->name,
            'assignee_name' => $this->task->assignee->name,
        ];
    }
} 