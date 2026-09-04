<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * Display the interactive calendar view.
     */
    public function index()
    {
        $projects = Project::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('calendar.index', compact('projects', 'users'));
    }

    /**
     * Fetch calendar events as JSON feed for FullCalendar.
     */
    public function events(Request $request): JsonResponse
    {
        $type = $request->query('type', 'all');
        $projectId = $request->query('project_id');
        $priority = $request->query('priority');
        $status = $request->query('status');

        $events = [];

        // 1. Fetch Task Events
        if ($type !== 'projects') {
            $taskQuery = Task::with(['project', 'assignee'])
                ->where(function ($q) {
                    $q->whereNotNull('due_date')
                      ->orWhereNotNull('start_date');
                });

            if ($projectId) {
                $taskQuery->where('project_id', $projectId);
            }

            if ($priority) {
                $taskQuery->where('priority', $priority);
            }

            if ($status) {
                $taskQuery->where('status', $status);
            }

            $tasks = $taskQuery->get();

            foreach ($tasks as $task) {
                $startDate = $task->start_date ?? $task->due_date;
                $endDate = $task->due_date ? Carbon::parse($task->due_date)->addDay()->format('Y-m-d') : null;

                // Color by priority / status
                $color = match (true) {
                    $task->status === 'completed' => '#10b981', // Green
                    $task->priority === 'high' => '#ef4444',    // Red
                    $task->priority === 'medium' => '#0284c7',  // Blue
                    default => '#64748b',                       // Slate
                };

                $events[] = [
                    'id' => 'task_' . $task->id,
                    'title' => '[Task] ' . $task->title,
                    'start' => $startDate,
                    'end' => $endDate,
                    'allDay' => true,
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'type' => 'task',
                        'item_id' => $task->id,
                        'title' => $task->title,
                        'category' => $task->type_display,
                        'category_badge' => $task->type_badge_class,
                        'project_name' => $task->project->name ?? 'General',
                        'project_url' => $task->project ? route('projects.show', $task->project) : '#',
                        'assignee_name' => $task->assignee->name ?? 'Unassigned',
                        'assignee_avatar' => $task->assignee ? $task->assignee->avatar_url : asset('assets/img/users/user_1.jpg'),
                        'status' => ucwords(str_replace('_', ' ', $task->status)),
                        'priority' => ucfirst($task->priority),
                        'start_date' => $task->start_date ? Carbon::parse($task->start_date)->format('M d, Y') : 'Not Set',
                        'due_date' => $task->due_date ? Carbon::parse($task->due_date)->format('M d, Y') : 'Not Set',
                        'is_overdue' => $task->due_date && Carbon::parse($task->due_date)->isPast() && $task->status !== 'completed',
                        'view_url' => route('tasks.show', $task),
                        'edit_url' => route('tasks.edit', $task),
                    ],
                ];
            }
        }

        // 2. Fetch Project Events
        if ($type !== 'tasks') {
            $projectQuery = Project::with('owner')
                ->where(function ($q) {
                    $q->whereNotNull('due_date')
                      ->orWhereNotNull('start_date');
                });

            if ($projectId) {
                $projectQuery->where('id', $projectId);
            }

            if ($priority) {
                $projectQuery->where('priority', $priority);
            }

            if ($status) {
                $projectQuery->where('status', $status);
            }

            $projects = $projectQuery->get();

            foreach ($projects as $project) {
                $startDate = $project->start_date ?? $project->due_date;
                $endDate = $project->due_date ? Carbon::parse($project->due_date)->addDay()->format('Y-m-d') : null;

                $color = '#6366f1'; // Indigo for projects

                $events[] = [
                    'id' => 'project_' . $project->id,
                    'title' => '📁 ' . $project->name,
                    'start' => $startDate,
                    'end' => $endDate,
                    'allDay' => true,
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'type' => 'project',
                        'item_id' => $project->id,
                        'title' => $project->name,
                        'category' => $project->type_display,
                        'category_badge' => $project->type_badge_class,
                        'project_name' => $project->name,
                        'project_url' => route('projects.show', $project),
                        'assignee_name' => $project->owner->name ?? 'Admin',
                        'assignee_avatar' => asset('assets/img/users/user_1.jpg'),
                        'status' => ucwords(str_replace('_', ' ', $project->status)),
                        'priority' => ucfirst($project->priority),
                        'start_date' => $project->start_date ? Carbon::parse($project->start_date)->format('M d, Y') : 'Not Set',
                        'due_date' => $project->due_date ? Carbon::parse($project->due_date)->format('M d, Y') : 'Not Set',
                        'is_overdue' => $project->due_date && Carbon::parse($project->due_date)->isPast() && $project->status !== 'completed',
                        'view_url' => route('projects.show', $project),
                        'edit_url' => route('projects.edit', $project),
                    ],
                ];
            }
        }

        return response()->json($events);
    }
}
