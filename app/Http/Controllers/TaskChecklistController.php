<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskChecklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskChecklistController extends Controller
{
    /**
     * Store a newly created checklist item for a task.
     */
    public function store(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $maxOrder = $task->checklists()->max('order') ?? 0;

        $checklist = $task->checklists()->create([
            'title' => $validated['title'],
            'order' => $maxOrder + 1,
            'is_completed' => false,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Checklist item added.',
                'checklist' => $checklist->load('completedBy'),
                'stats' => [
                    'total' => $task->fresh()->checklist_total_count,
                    'completed' => $task->fresh()->checklist_completed_count,
                    'percentage' => $task->fresh()->checklist_progress_percentage,
                ],
            ]);
        }

        return redirect()->route('tasks.show', $task)->with('success', 'Checklist item added successfully.');
    }

    /**
     * Toggle the completion status of a checklist item.
     */
    public function toggle(Request $request, TaskChecklist $checklist)
    {
        $newStatus = !$checklist->is_completed;

        $checklist->update([
            'is_completed' => $newStatus,
            'completed_at' => $newStatus ? now() : null,
            'completed_by' => $newStatus ? Auth::id() : null,
        ]);

        $task = $checklist->task->fresh();

        if ($newStatus) {
            $userName = Auth::user()?->name ?? 'User';
            \App\Models\ActivityLog::record(
                $task->project_id,
                'checklist_completed',
                "{$userName} completed subtask '{$checklist->title}' on task '{$task->title}'",
                $task->id,
                ['checklist_id' => $checklist->id]
            );
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'is_completed' => $checklist->is_completed,
                'completed_at' => $checklist->completed_at ? $checklist->completed_at->format('d M Y, h:i A') : null,
                'completed_by_name' => $checklist->completedBy ? $checklist->completedBy->name : null,
                'stats' => [
                    'total' => $task->checklist_total_count,
                    'completed' => $task->checklist_completed_count,
                    'percentage' => $task->checklist_progress_percentage,
                ],
            ]);
        }

        return redirect()->route('tasks.show', $checklist->task_id)->with('success', 'Checklist item updated.');
    }

    /**
     * Remove a checklist item.
     */
    public function destroy(Request $request, TaskChecklist $checklist)
    {
        $taskId = $checklist->task_id;
        $task = $checklist->task;
        $checklist->delete();

        if ($request->wantsJson() || $request->ajax()) {
            $taskFresh = $task->fresh();
            return response()->json([
                'success' => true,
                'message' => 'Checklist item removed.',
                'stats' => [
                    'total' => $taskFresh ? $taskFresh->checklist_total_count : 0,
                    'completed' => $taskFresh ? $taskFresh->checklist_completed_count : 0,
                    'percentage' => $taskFresh ? $taskFresh->checklist_progress_percentage : 0,
                ],
            ]);
        }

        return redirect()->route('tasks.show', $taskId)->with('success', 'Checklist item deleted.');
    }
}
