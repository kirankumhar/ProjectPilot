<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return view('projects.index', compact('projects', 'stats'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('projects.create', compact('users'));
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

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully!');
    }

    /**
     * Display the specified project details.
     */
    public function show(Project $project)
    {
        $project->load(['owner', 'members', 'tasks.assignee']);
        $allUsers = User::orderBy('name')->get();

        $taskTypesCount = [
            'feature' => $project->tasks->where('type', 'feature')->count(),
            'bug_fix' => $project->tasks->where('type', 'bug_fix')->count(),
            'maintenance' => $project->tasks->where('type', 'maintenance')->count(),
            'support' => $project->tasks->where('type', 'support')->count(),
            'cr' => $project->tasks->where('type', 'cr')->count(),
        ];

        return view('projects.show', compact('project', 'allUsers', 'taskTypesCount'));
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project)
    {
        $users = User::orderBy('name')->get();
        $project->load('members');

        return view('projects.edit', compact('project', 'users'));
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

        $project->update($validated);

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
