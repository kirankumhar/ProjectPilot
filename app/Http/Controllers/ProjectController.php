<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProjectController extends Controller
{
    /**
     * Display a listing of the projects.
     */
    public function index(Request $request)
    {
        $query = Project::with(['owner', 'tasks', 'members']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $projects = $query->latest()->paginate(9)->withQueryString();

        $stats = [
            'total' => Project::count(),
            'new_dev' => Project::where('type', 'new_development')->count(),
            'maintenance' => Project::where('type', 'maintenance')->count(),
        ];

        $allUsers = User::orderBy('name')->get(['id', 'name', 'email']);

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'stats' => $stats,
            'filters' => $request->only(['search', 'type', 'status', 'priority']),
            'allUsers' => $allUsers,
        ]);
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        return Inertia::render('Projects/Create', [
            'users' => $users
        ]);
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:new_development,maintenance',
            'url' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed,on_hold',
            'priority' => 'required|in:low,medium,high',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
        ]);

        $validated['user_id'] = Auth::id() ?? 1;

        $project = Project::create($validated);

        if (!empty($validated['members'])) {
            $project->members()->sync($validated['members']);
        } else {
            // Attach creator by default
            $project->members()->sync([$validated['user_id']]);
        }

        $userName = Auth::user()?->name ?? 'User';
        \App\Models\ActivityLog::record(
            $project->id,
            'project_created',
            "{$userName} created project {$project->name}"
        );

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully!');
    }

    /**
     * Display the specified project details.
     */
    public function show(Project $project)
    {
        $project->load(['owner', 'members', 'tasks.assignee', 'tasks.comments', 'activities.user']);
        $allUsers = User::orderBy('name')->get(['id', 'name', 'email']);

        $taskTypesCount = [
            'feature' => $project->tasks->where('type', 'feature')->count(),
            'bug_fix' => $project->tasks->where('type', 'bug_fix')->count(),
            'maintenance' => $project->tasks->where('type', 'maintenance')->count(),
            'support' => $project->tasks->where('type', 'support')->count(),
            'cr' => $project->tasks->where('type', 'cr')->count(),
        ];

        return Inertia::render('Projects/Show', [
            'project' => $project,
            'allUsers' => $allUsers,
            'taskTypesCount' => $taskTypesCount,
        ]);
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project)
    {
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        $project->load('members');

        return Inertia::render('Projects/Edit', [
            'project' => $project,
            'users' => $users,
        ]);
    }

    /**
     * Update the specified project in storage.
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:new_development,maintenance',
            'url' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed,on_hold',
            'priority' => 'required|in:low,medium,high',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
        ]);

        $oldStatus = $project->status;
        $project->update($validated);

        if ($oldStatus !== $project->status) {
            $userName = Auth::user()?->name ?? 'User';
            $formattedOld = ucwords(str_replace('_', ' ', $oldStatus));
            $formattedNew = ucwords(str_replace('_', ' ', $project->status));
            \App\Models\ActivityLog::record(
                $project->id,
                'project_updated',
                "{$userName} changed project status from '{$formattedOld}' to '{$formattedNew}'",
                null,
                ['old_status' => $oldStatus, 'new_status' => $project->status]
            );
        }

        if (isset($validated['members'])) {
            $project->members()->sync($validated['members']);
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project updated successfully!');
    }

    /**
     * Remove the specified project from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully!');
    }
}
