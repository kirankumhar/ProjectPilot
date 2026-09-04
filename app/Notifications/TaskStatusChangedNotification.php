<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Task $task,
        public ?User $changedBy = null
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $updaterName = $this->changedBy?->name ?? 'A team member';
        $statusDisplay = ucwords(str_replace('_', ' ', $this->task->status));

        return [
            'type' => 'task_status_changed',
            'title' => 'Task Status Updated',
            'message' => "{$updaterName} updated '{$this->task->title}' to {$statusDisplay}.",
            'url' => route('tasks.show', $this->task),
            'icon' => 'fa-refresh',
            'icon_color' => 'text-info',
            'task_id' => $this->task->id,
            'status' => $this->task->status,
        ];
    }
}
