<x-layouts.app title="Tasks - ProjectPilot">
    <div class="page-heading">
        <div class="page-heading__container">
            <h1 class="title">Tasks Management</h1>
            <p class="caption">View, assign, and track progress of Features, Bug Fixes & Maintenance tasks</p>
        </div>
        <div class="page-heading__container float-right">
            <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                <i class="fa fa-plus-circle margin-right-5"></i> Create Task
            </a>
        </div>
    </div>

    <div class="container-fluid">
        <!-- FILTER BAR -->
        <div class="card margin-bottom-20">
            <div class="card-body">
                <form action="{{ route('tasks.index') }}" method="GET" class="form-row align-items-center">
                    <div class="col-12 col-md-3 margin-bottom-10">
                        <input type="text" name="search" class="form-control" placeholder="Search task title..." value="{{ request('search') }}">
                    </div>
                    <div class="col-6 col-md-2 margin-bottom-10">
                        <select name="type" class="form-control">
                            <option value="">All Task Types</option>
                            <option value="feature" {{ request('type') === 'feature' ? 'selected' : '' }}>Feature</option>
                            <option value="bug_fix" {{ request('type') === 'bug_fix' ? 'selected' : '' }}>Bug Fix</option>
                            <option value="maintenance" {{ request('type') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="support" {{ request('type') === 'support' ? 'selected' : '' }}>Support Ticket</option>
                            <option value="cr" {{ request('type') === 'cr' ? 'selected' : '' }}>Change Request</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-2 margin-bottom-10">
                        <select name="project_id" class="form-control">
                            <option value="">All Projects</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>
                                    {{ Str::limit($p->name, 25) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2 margin-bottom-10">
                        <select name="status" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3 margin-bottom-10 d-flex">
                        <button type="submit" class="btn btn-secondary btn-block margin-right-5">
                            <i class="fa fa-filter margin-right-5"></i> Filter
                        </button>
                        <a href="{{ route('tasks.index') }}" class="btn btn-light" title="Reset Filters">
                            <i class="fa fa-refresh"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- TASKS TABLE CARD -->
        <div class="card margin-bottom-20">
            <div class="card-body padding-top-10">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle margin-bottom-0">
                        <thead>
                            <tr>
                                <th width="30%">Task Title</th>
                                <th>Category / Type</th>
                                <th>Project</th>
                                <th>Assignee</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Due Date</th>
                                <th width="100">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tasks as $task)
                                <tr>
                                    <td>
                                        <a href="{{ route('tasks.edit', $task) }}" class="font-weight-bold text-dark">
                                            {{ $task->title }}
                                        </a>
                                        @if($task->description)
                                            <div class="small text-muted">{{ Str::limit($task->description, 50) }}</div>
                                        @endif
                                        @if($task->attachment)
                                            <a href="{{ $task->attachment_url }}" target="_blank" class="badge badge-info mt-1" title="{{ $task->attachment_name }}">
                                                <i class="fa fa-paperclip margin-right-5"></i> Attachment
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $task->type_badge_class }}">
                                            {{ $task->type_display }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('projects.show', $task->project) }}" class="badge badge-light border">
                                            <i class="fa fa-folder margin-right-5 text-primary"></i>{{ $task->project->name ?? 'General' }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $task->assignee ? $task->assignee->avatar_url : asset('assets/img/users/user_1.jpg') }}" alt="Assignee" class="rounded-circle margin-right-5" style="width: 24px; height: 24px; object-fit: cover;">
                                            <small class="font-weight-bold">{{ $task->assignee->name ?? 'Unassigned' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($task->priority === 'high')
                                            <span class="badge badge-danger">High</span>
                                        @elseif($task->priority === 'medium')
                                            <span class="badge badge-info">Medium</span>
                                        @else
                                            <span class="badge badge-light">Low</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($task->status === 'completed')
                                            <span class="badge badge-success">Completed</span>
                                        @elseif($task->status === 'in_progress')
                                            <span class="badge badge-primary">In Progress</span>
                                        @else
                                            <span class="badge badge-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('M d, Y') : 'N/A' }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-light btn-sm btn-icon" title="Edit">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-light btn-sm btn-icon text-danger" title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
                                        <i class="fa fa-tasks fa-2x margin-bottom-10 d-block"></i>
                                        No tasks found matching your filter criteria. 
                                        <a href="{{ route('tasks.create') }}">Create a new task</a>.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- PAGINATION -->
        <div class="d-flex justify-content-center margin-top-20">
            {{ $tasks->links('pagination::bootstrap-4') }}
        </div>
    </div>
</x-layouts.app>
