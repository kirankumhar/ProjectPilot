<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProjects = Project::count();
        $activeProjects = Project::where('status', 'in_progress')->count();
        $completedProjects = Project::where('status', 'completed')->count();
        
        $totalTasks = Task::count();
        $pendingTasks = Task::where('status', 'pending')->count();
        $completedTasks = Task::where('status', 'completed')->count();
        $totalMembers = User::count();

        $recentProjects = Project::with(['owner', 'tasks', 'members'])
            ->latest()
            ->take(5)
            ->get();

        $recentTasks = Task::with(['project', 'assignee'])
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard.index', compact(
            'totalProjects',
            'activeProjects',
            'completedProjects',
            'totalTasks',
            'pendingTasks',
            'completedTasks',
            'totalMembers',
            'recentProjects',
            'recentTasks'
        ));
    }
}
