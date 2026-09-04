<?php

namespace App\Notifications;

use App\Models\TaskComment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskCommentNotification extends Notification
{
    use Queueable;

    public function __construct(public TaskComment $comment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $authorName = $this->comment->user->name ?? 'A team member';
        $taskTitle = $this->comment->task->title ?? 'Task';

        return [
            'type' => 'task_comment',
            'title' => 'New Comment on Task',
            'message' => "{$authorName} commented on '{$taskTitle}'.",
            'url' => route('tasks.show', $this->comment->task_id) . '#comments-section',
            'icon' => 'fa-comments',
            'icon_color' => 'text-success',
            'task_id' => $this->comment->task_id,
            'comment_id' => $this->comment->id,
        ];
    }
}
