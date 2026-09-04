<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskCommentController extends Controller
{
    /**
     * Store a newly created comment on a task.
     */
    public function store(Request $request, Task $task)
    {
        $validated = $request->validate([
            'comment' => ['required', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,zip,rar,txt', 'max:10240'],
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('comment_attachments', 'public');
        }

        $comment = $task->comments()->create([
            'user_id' => Auth::id(),
            'comment' => $validated['comment'],
            'attachment' => $attachmentPath,
        ]);

        $comment->load(['task.project', 'user']);

        // Notify task assignee if not the commenter
        if ($task->assigned_to && $task->assigned_to !== Auth::id()) {
            $task->assignee?->notify(new \App\Notifications\TaskCommentNotification($comment));
        }

        // Notify project owner if different from commenter and assignee
        if ($task->project && $task->project->user_id && $task->project->user_id !== Auth::id() && $task->project->user_id !== $task->assigned_to) {
            $task->project->owner?->notify(new \App\Notifications\TaskCommentNotification($comment));
        }

        if ($request->ajax() || $request->wantsJson()) {
            $comment->load('user');
            return response()->json([
                'status' => 'success',
                'message' => 'Comment posted successfully!',
                'comment' => $comment,
            ]);
        }

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Comment posted successfully!');
    }

    /**
     * Remove the specified comment.
     */
    public function destroy(TaskComment $comment)
    {
        $currentUser = Auth::user();

        if ($comment->user_id !== $currentUser->id && !$currentUser->isAdmin()) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized to delete this comment.',
                ], 403);
            }
            abort(403, 'Unauthorized to delete this comment.');
        }

        if ($comment->attachment && Storage::disk('public')->exists($comment->attachment)) {
            Storage::disk('public')->delete($comment->attachment);
        }

        $taskId = $comment->task_id;
        $comment->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Comment deleted successfully.',
            ]);
        }

        return redirect()->route('tasks.show', $taskId)
            ->with('success', 'Comment deleted successfully!');
    }
}
