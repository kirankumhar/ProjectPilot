<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(public Task $task)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'task_assigned',
            'title' => 'New Task Assigned',
            'message' => "You have been assigned to '{$this->task->title}' in project '{$this->task->project->name}'.",
            'url' => route('tasks.show', $this->task),
            'icon' => 'fa-tasks',
            'icon_color' => 'text-primary',
            'task_id' => $this->task->id,
            'project_id' => $this->task->project_id,
        ];
    }
}
