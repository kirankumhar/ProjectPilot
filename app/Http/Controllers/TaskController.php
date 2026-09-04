<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks.
     */
    public function index(Request $request)
    {
        $query = Task::with(['project', 'assignee', 'checklists'])->withCount('comments');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $viewMode = $request->get('view', 'board');
        
        if ($viewMode === 'board') {
            $tasks = (clone $query)->latest()->get();
        } else {
            $tasks = $query->latest()->paginate(12)->withQueryString();
        }

        $allTasksForBoard = (clone $query)->latest()->get();
        $projects = Project::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('tasks.index', compact('tasks', 'allTasksForBoard', 'projects', 'users', 'viewMode'));
    }

    /**
     * Show the form for creating a new task.
     */
    public function create(Request $request)
    {
        $projects = Project::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $selectedProjectId = $request->query('project_id');

        return view('tasks.create', compact('projects', 'users', 'selectedProjectId'));
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:feature,bug_fix,maintenance,support,cr',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'priority' => 'required|in:low,medium,high',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'required|exists:users,id',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,zip,rar,txt|max:10240',
            'estimated_hours' => 'nullable|numeric|min:0|max:9999',
        ]);

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('attachments', 'public');
        }

        $task = Task::create($validated);
        $task->load('project');

        $userName = Auth::user()?->name ?? 'User';
        \App\Models\ActivityLog::record(
            $task->project_id,
            'task_created',
            "{$userName} created task #TSK-{$task->id} ({$task->title})",
            $task->id,
            ['type' => $task->type, 'priority' => $task->priority]
        );

        // Notify assigned user if different from creator
        if ($task->assigned_to && $task->assigned_to !== auth()->id()) {
            $assignee = User::find($task->assigned_to);
            $assignee?->notify(new \App\Notifications\TaskAssignedNotification($task));
        }

        if ($request->filled('redirect_to_project')) {
            return redirect()->route('projects.show', $validated['project_id'])
                ->with('success', 'Task added successfully!');
        }

        return redirect()->route('tasks.index')
            ->with('success', 'Task created successfully!');
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task)
    {
        $task->load([
            'project',
            'assignee',
            'comments.user',
            'timeLogs.user',
            'checklists.completedBy',
            'activities.user',
        ]);

        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task)
    {
        $projects = Project::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('tasks.edit', compact('task', 'projects', 'users'));
    }

    /**
     * Update the specified task in storage.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:feature,bug_fix,maintenance,support,cr',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'priority' => 'required|in:low,medium,high',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'required|exists:users,id',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,zip,rar,txt|max:10240',
            'estimated_hours' => 'nullable|numeric|min:0|max:9999',
        ]);

        if ($request->hasFile('attachment')) {
            if ($task->attachment && Storage::disk('public')->exists($task->attachment)) {
                Storage::disk('public')->delete($task->attachment);
            }
            $validated['attachment'] = $request->file('attachment')->store('attachments', 'public');
        }

        $previousAssigneeId = $task->assigned_to;
        $oldStatus = $task->status;
        $task->update($validated);
        $task->load('project');

        $userName = Auth::user()?->name ?? 'User';
        if ($oldStatus !== $task->status) {
            $formattedOld = ucwords(str_replace('_', ' ', $oldStatus));
            $formattedNew = ucwords(str_replace('_', ' ', $task->status));
            \App\Models\ActivityLog::record(
                $task->project_id,
                'task_status_changed',
                "{$userName} changed status of task '{$task->title}' from {$formattedOld} to {$formattedNew}",
                $task->id,
                ['old_status' => $oldStatus, 'new_status' => $task->status]
            );
        }

        // If assignee changed and is not the current user, notify new assignee
        if ($task->assigned_to && $task->assigned_to !== $previousAssigneeId && $task->assigned_to !== auth()->id()) {
            $newAssignee = User::find($task->assigned_to);
            $newAssignee?->notify(new \App\Notifications\TaskAssignedNotification($task));

            \App\Models\ActivityLog::record(
                $task->project_id,
                'task_reassigned',
                "{$userName} assigned task '{$task->title}' to {$newAssignee?->name}",
                $task->id
            );
        }

        return redirect()->route('tasks.index')
            ->with('success', 'Task updated successfully!');
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Task $task)
    {
        $projectId = $task->project_id;
        $task->delete();

        if (request()->has('redirect_to_project')) {
            return redirect()->route('projects.show', $projectId)
                ->with('success', 'Task deleted successfully!');
        }

        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted successfully!');
    }

    /**
     * Update task status via AJAX drag and drop.
     */
    public function updateStatus(Request $request, Task $task)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $oldStatus = $task->status;
        $task->update([
            'status' => $validated['status'],
        ]);
        $task->load(['project', 'assignee']);

        if ($oldStatus !== $task->status) {
            $userName = Auth::user()?->name ?? 'User';
            $formattedOld = ucwords(str_replace('_', ' ', $oldStatus));
            $formattedNew = ucwords(str_replace('_', ' ', $task->status));
            \App\Models\ActivityLog::record(
                $task->project_id,
                'task_status_changed',
                "{$userName} changed status of task '{$task->title}' from {$formattedOld} to {$formattedNew}",
                $task->id,
                ['old_status' => $oldStatus, 'new_status' => $task->status]
            );
        }

        // Notify assignee if not current user
        if ($task->assigned_to && $task->assigned_to !== auth()->id()) {
            $task->assignee?->notify(new \App\Notifications\TaskStatusChangedNotification($task, auth()->user()));
        }

        // Notify project owner if different from current user and assignee
        if ($task->project && $task->project->user_id && $task->project->user_id !== auth()->id() && $task->project->user_id !== $task->assigned_to) {
            $task->project->owner?->notify(new \App\Notifications\TaskStatusChangedNotification($task, auth()->user()));
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Task status updated to ' . ucwords(str_replace('_', ' ', $task->status)),
                'status' => $task->status,
                'task_id' => $task->id,
            ]);
        }

        return redirect()->back()->with('success', 'Task status updated successfully!');
    }
}
