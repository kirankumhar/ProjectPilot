<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskTimeLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimeLogController extends Controller
{
    /**
     * Display the team timesheet and work logs dashboard.
     */
    public function index(Request $request)
    {
        $query = TaskTimeLog::with(['task.project', 'user']);

        // 1. Period filter
        $period = $request->query('period', 'all');
        if ($period === 'today') {
            $query->whereDate('logged_date', Carbon::today());
        } elseif ($period === 'this_week') {
            $query->whereBetween('logged_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($period === 'this_month') {
            $query->whereBetween('logged_date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
        }

        // 2. Project filter
        if ($request->filled('project_id')) {
            $query->whereHas('task', function ($q) use ($request) {
                $q->where('project_id', $request->project_id);
            });
        }

        // 3. User filter
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Calculate summary stats on filtered query
        $statsQuery = clone $query;
        $totalHours = (float) $statsQuery->sum('hours');
        $totalLogsCount = $statsQuery->count();
        $activeContributorsCount = $statsQuery->distinct('user_id')->count('user_id');

        $timeLogs = $query->orderBy('logged_date', 'desc')->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $projects = Project::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('timesheets.index', compact(
            'timeLogs',
            'projects',
            'users',
            'totalHours',
            'totalLogsCount',
            'activeContributorsCount',
            'period'
        ));
    }

    /**
     * Store a new work log entry on a task.
     */
    public function store(Request $request, Task $task)
    {
        $validated = $request->validate([
            'hours' => ['required', 'numeric', 'min:0.1', 'max:24'],
            'logged_date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $task->timeLogs()->create([
            'user_id' => Auth::id(),
            'hours' => $validated['hours'],
            'logged_date' => $validated['logged_date'],
            'note' => $validated['note'] ?? null,
        ]);

        $userName = Auth::user()?->name ?? 'User';
        \App\Models\ActivityLog::record(
            $task->project_id,
            'time_logged',
            "{$userName} logged {$validated['hours']} hours on task '{$task->title}'",
            $task->id,
            ['hours' => $validated['hours'], 'date' => $validated['logged_date']]
        );

        return redirect()->route('tasks.show', $task)
            ->with('success', "Logged {$validated['hours']} hours successfully!");
    }

    /**
     * Remove a work log entry.
     */
    public function destroy(TaskTimeLog $timeLog)
    {
        $currentUser = Auth::user();

        if ($timeLog->user_id !== $currentUser->id && !$currentUser->isAdmin()) {
            abort(403, 'Unauthorized to delete this work log.');
        }

        $taskId = $timeLog->task_id;
        $timeLog->delete();

        return redirect()->back()->with('success', 'Work log entry deleted.');
    }
}
