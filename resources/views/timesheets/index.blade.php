<x-layouts.app title="Team Timesheets - ProjectPilot">
    <div class="page-heading">
        <div class="page-heading__container">
            <h1 class="title">Team Timesheets & Work Logs</h1>
            <p class="caption">Track hours spent on tasks, monitor team output, and audit project delivery time</p>
        </div>
        <div class="page-heading__container float-right d-none d-sm-block">
            <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary margin-right-5">
                <i class="fa fa-tasks margin-right-5"></i> View Tasks Board
            </a>
            <a href="{{ route('calendar.index') }}" class="btn btn-outline-info">
                <i class="fa fa-calendar margin-right-5"></i> View Calendar
            </a>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Timesheets</li>
            </ol>
        </nav>
    </div>

    <div class="container-fluid">
        <!-- METRICS ROW -->
        <div class="row margin-bottom-20">
            <div class="col-12 col-md-4 margin-bottom-10">
                <div class="card border shadow-sm">
                    <div class="card-body d-flex align-items-center justify-content-between p-3">
                        <div>
                            <span class="text-muted text-uppercase small font-weight-bold">Total Logged Hours</span>
                            <h2 class="font-weight-bold text-primary mb-0 mt-1">{{ round($totalHours, 2) }} <small class="text-muted" style="font-size: 1rem;">hrs</small></h2>
                        </div>
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa fa-clock-o fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4 margin-bottom-10">
                <div class="card border shadow-sm">
                    <div class="card-body d-flex align-items-center justify-content-between p-3">
                        <div>
                            <span class="text-muted text-uppercase small font-weight-bold">Total Work Logs</span>
                            <h2 class="font-weight-bold text-success mb-0 mt-1">{{ $totalLogsCount }}</h2>
                        </div>
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa fa-pencil-square-o fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4 margin-bottom-10">
                <div class="card border shadow-sm">
                    <div class="card-body d-flex align-items-center justify-content-between p-3">
                        <div>
                            <span class="text-muted text-uppercase small font-weight-bold">Active Contributors</span>
                            <h2 class="font-weight-bold text-info mb-0 mt-1">{{ $activeContributorsCount }}</h2>
                        </div>
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa fa-users fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FILTER BAR -->
        <div class="card margin-bottom-20 shadow-sm border">
            <div class="card-body">
                <form action="{{ route('timesheets.index') }}" method="GET" class="form-row align-items-end">
                    <div class="col-12 col-md-3 margin-bottom-10">
                        <label class="small font-weight-bold text-muted mb-1">Timeframe</label>
                        <select name="period" class="form-control">
                            <option value="all" {{ request('period') === 'all' || !request('period') ? 'selected' : '' }}>All Time</option>
                            <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Today</option>
                            <option value="this_week" {{ request('period') === 'this_week' ? 'selected' : '' }}>This Week</option>
                            <option value="this_month" {{ request('period') === 'this_month' ? 'selected' : '' }}>This Month</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-3 margin-bottom-10">
                        <label class="small font-weight-bold text-muted mb-1">Project</label>
                        <select name="project_id" class="form-control">
                            <option value="">All Projects</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>
                                    {{ Str::limit($p->name, 25) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-3 margin-bottom-10">
                        <label class="small font-weight-bold text-muted mb-1">Developer / User</label>
                        <select name="user_id" class="form-control">
                            <option value="">All Team Members</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->role_display }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-3 margin-bottom-10 d-flex">
                        <button type="submit" class="btn btn-secondary btn-block mr-2">
                            <i class="fa fa-filter mr-1"></i> Filter
                        </button>
                        <a href="{{ route('timesheets.index') }}" class="btn btn-light" title="Reset Filters">
                            <i class="fa fa-refresh"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- TIMESHEET ENTRIES TABLE -->
        <div class="card shadow-sm border">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th width="120">Date</th>
                                <th>Developer</th>
                                <th>Task Title</th>
                                <th>Project</th>
                                <th width="110">Logged</th>
                                <th>Work Description / Notes</th>
                                <th width="60" class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($timeLogs as $log)
                                <tr>
                                    <td>
                                        <span class="font-weight-bold text-dark">
                                            {{ \Carbon\Carbon::parse($log->logged_date)->format('M d, Y') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $log->user->avatar_url ?? asset('assets/img/users/user_1.jpg') }}" 
                                                 alt="{{ $log->user->name ?? 'User' }}" 
                                                 class="rounded-circle mr-2 border" 
                                                 style="width: 28px; height: 28px; object-fit: cover;">
                                            <div>
                                                <strong class="text-dark">{{ $log->user->name ?? 'User' }}</strong>
                                                @if(auth()->id() === $log->user_id)
                                                    <span class="badge badge-info ml-1" style="font-size: 0.65rem;">You</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('tasks.show', $log->task) }}" class="font-weight-bold text-primary">
                                            {{ $log->task->title }}
                                        </a>
                                        @if($log->task->type)
                                            <span class="badge {{ $log->task->type_badge_class }} ml-1" style="font-size: 0.65rem;">
                                                {{ $log->task->type_display }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('projects.show', $log->task->project) }}" class="badge badge-light border">
                                            <i class="fa fa-folder text-primary mr-1"></i>{{ $log->task->project->name ?? 'General' }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary font-weight-bold" style="font-size: 0.85rem;">
                                            {{ $log->hours }} hrs
                                        </span>
                                    </td>
                                    <td>
                                        @if($log->note)
                                            <span class="text-muted small" style="line-height: 1.4;">{{ $log->note }}</span>
                                        @else
                                            <span class="text-muted font-italic small">- No note provided -</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if(auth()->id() === $log->user_id || (auth()->check() && auth()->user()->isAdmin()))
                                            <form action="{{ route('tasks.time-logs.destroy', $log) }}" method="POST" onsubmit="return confirm('Delete this work log entry?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-link text-danger p-0" title="Delete log">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fa fa-clock-o fa-3x text-muted mb-3 d-block"></i>
                                        <h5>No work logs found</h5>
                                        <p class="small text-muted mb-0">
                                            Hours logged on tasks will appear here in the team timesheet.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($timeLogs->hasPages())
                <div class="card-footer bg-white d-flex justify-content-center">
                    {{ $timeLogs->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
