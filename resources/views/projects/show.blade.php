<x-layouts.app :title="$project->name . ' - ProjectPilot'">
    <!-- PAGE HEADING -->
    <div class="page-heading">
        <div class="page-heading__container">
            <h1 class="title">{{ $project->name }}</h1>
            <p class="caption">Project ID: #PRJ-{{ str_pad($project->id, 4, '0', STR_PAD_LEFT) }}</p>
        </div>
        <div class="page-heading__container float-right d-none d-sm-block">
            @if($project->url)
                <a href="{{ $project->url }}" target="_blank" class="btn btn-info margin-right-5" title="Visit Project URL">
                    <i class="fa fa-external-link margin-right-5"></i> Visit Link
                </a>
            @endif
            <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-secondary margin-right-5">
                <i class="fa fa-pencil margin-right-5"></i> Edit Project
            </a>
            <a href="{{ route('tasks.create', ['project_id' => $project->id]) }}" class="btn btn-primary">
                <i class="fa fa-plus-circle margin-right-5"></i> Add Task
            </a>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projects</a></li>
                <li class="breadcrumb-item active">{{ Str::limit($project->name, 25) }}</li>
            </ol>
        </nav>
    </div>
    <!-- //END PAGE HEADING -->

    <div class="container-fluid">
        <div class="form-row">
            <!-- LEFT COLUMN: Project Stats, Overview & Tasks -->
            <div class="col-12 col-lg-8">
                <!-- MEMBERS & STATUS CARD -->
                <div class="card margin-bottom-20">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-6 margin-bottom-15">
                                <h4>Project Owner</h4>
                                <p class="subtitle margin-bottom-15">Created by</p>
                                <div class="user user--bordered user--lg">
                                    <img src="{{ asset('assets/img/users/user_1.jpg') }}" alt="Owner Avatar">
                                    <div class="user__name">
                                        <strong>{{ $project->owner->name ?? 'Admin User' }}</strong><br>
                                        <span class="text-muted">{{ $project->owner->email ?? 'admin@projectpilot.com' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 margin-bottom-15">
                                <h4>Team Members</h4>
                                <p class="subtitle margin-bottom-15">Assigned contributors</p>
                                <div class="d-flex flex-wrap align-items-center">
                                    @forelse($project->members as $member)
                                        <div class="user user--bordered user--lg margin-right-10 margin-bottom-10" title="{{ $member->name }}">
                                            <img src="{{ asset('assets/img/users/user_' . (($loop->index % 8) + 1) . '.jpg') }}" alt="{{ $member->name }}">
                                            <div class="user__name">
                                                <strong>{{ $member->name }}</strong><br>
                                                <small class="text-muted">{{ $member->role_display }}</small>
                                            </div>
                                        </div>
                                    @empty
                                        <span class="text-muted">No specific members assigned.</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="divider-text margin-top-0 margin-bottom-0">Project Info & Timeline</div>
                    
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 col-md-3 margin-bottom-10">
                                <strong>Category / Type</strong><br>
                                <span class="badge {{ $project->type_badge_class }}">
                                    <i class="fa {{ $project->isMaintenance() ? 'fa-wrench' : 'fa-rocket' }} margin-right-5"></i>{{ $project->type_display }}
                                </span>
                            </div>
                            <div class="col-6 col-md-3 margin-bottom-10">
                                <strong>Status</strong><br>
                                @if($project->status === 'in_progress')
                                    <span class="badge badge-primary">In Progress</span>
                                @elseif($project->status === 'completed')
                                    <span class="badge badge-success">Completed</span>
                                @elseif($project->status === 'on_hold')
                                    <span class="badge badge-warning">On Hold</span>
                                @else
                                    <span class="badge badge-secondary">Pending</span>
                                @endif
                            </div>
                            <div class="col-6 col-md-3 margin-bottom-10">
                                <strong>Priority</strong><br>
                                @if($project->priority === 'high')
                                    <span class="badge badge-danger">High Priority</span>
                                @elseif($project->priority === 'medium')
                                    <span class="badge badge-info">Medium</span>
                                @else
                                    <span class="badge badge-light">Low</span>
                                @endif
                            </div>
                            <div class="col-6 col-md-3 margin-bottom-10">
                                <strong>Due Date</strong><br>
                                {{ $project->due_date ? \Carbon\Carbon::parse($project->due_date)->format('d M Y') : 'N/A' }}
                            </div>
                            @if($project->url)
                                <div class="col-12 margin-top-10">
                                    <strong>Project Link / URL:</strong>
                                    <a href="{{ $project->url }}" target="_blank" class="text-primary font-weight-bold ml-1">
                                        {{ $project->url }} <i class="fa fa-external-link ml-1"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- PROJECT DESCRIPTION -->
                @if($project->description)
                    <div class="card margin-bottom-20">
                        <div class="card-body">
                            <h4>Project Description</h4>
                            <p class="margin-bottom-0">{{ $project->description }}</p>
                        </div>
                    </div>
                @endif

                <!-- TASKS LIST FOR THIS PROJECT -->
                <div class="card margin-bottom-20">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h4>Project Tasks ({{ $project->tasks->count() }})</h4>
                            <p class="subtitle margin-bottom-0">Milestones and work items for this project</p>
                        </div>
                        <div>
                            <a href="{{ route('tasks.index', ['project_id' => $project->id, 'view' => 'board']) }}" class="btn btn-sm btn-outline-primary margin-right-5">
                                <i class="fa fa-th-large margin-right-5"></i> View Board
                            </a>
                            <a href="{{ route('tasks.create', ['project_id' => $project->id]) }}" class="btn btn-sm btn-primary">
                                <i class="fa fa-plus margin-right-5"></i> Add Task
                            </a>
                        </div>
                    </div>
                    <div class="card-body padding-top-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered margin-bottom-0">
                                <thead>
                                    <tr>
                                        <th>Task Title</th>
                                        <th>Assignee</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th>Due Date</th>
                                        <th width="100">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($project->tasks as $task)
                                        <tr>
                                            <td>
                                                <a href="{{ route('tasks.show', $task) }}" class="text-bold text-dark">
                                                    {{ $task->title }}
                                                </a>
                                                @if($task->description)
                                                    <div class="small text-muted">{{ Str::limit($task->description, 40) }}</div>
                                                @endif
                                                <div class="mt-1">
                                                    @if($task->attachment)
                                                        <a href="{{ $task->attachment_url }}" target="_blank" class="badge badge-info mr-1" title="{{ $task->attachment_name }}">
                                                            <i class="fa fa-paperclip margin-right-5"></i> File
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('tasks.show', $task) }}#comments-section" class="badge {{ $task->comments->count() > 0 ? 'badge-primary' : 'badge-light text-muted border' }}" title="{{ $task->comments->count() }} comments">
                                                        <i class="fa fa-comments margin-right-5"></i>{{ $task->comments->count() }}
                                                    </a>
                                                    @if($task->checklist_total_count > 0)
                                                        <a href="{{ route('tasks.show', $task) }}#checklist-card" class="badge {{ $task->checklist_progress_percentage == 100 ? 'badge-success' : 'badge-light text-muted border' }} ml-1" title="Checklist: {{ $task->checklist_completed_count }}/{{ $task->checklist_total_count }} completed">
                                                            <i class="fa fa-check-square-o margin-right-5"></i>{{ $task->checklist_completed_count }}/{{ $task->checklist_total_count }}
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <small class="font-weight-bold">
                                                    <i class="fa fa-user margin-right-5 text-muted"></i>{{ $task->assignee->name ?? 'Unassigned' }}
                                                </small>
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
                                                @if($task->priority === 'high')
                                                    <span class="badge badge-danger">High</span>
                                                @elseif($task->priority === 'medium')
                                                    <span class="badge badge-info">Medium</span>
                                                @else
                                                    <span class="badge badge-light">Low</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('M d, Y') : 'N/A' }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('tasks.show', $task) }}" class="btn btn-light btn-sm btn-icon" title="View Details & Comments">
                                                        <span class="fa fa-eye"></span>
                                                    </a>
                                                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-light btn-sm btn-icon" title="Edit Task">
                                                        <span class="fa fa-pencil"></span>
                                                    </a>
                                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this task?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="redirect_to_project" value="1">
                                                        <button type="submit" class="btn btn-light btn-sm btn-icon text-danger" title="Delete Task">
                                                            <span class="fa fa-times"></span>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                No tasks added yet. 
                                                <a href="{{ route('tasks.create', ['project_id' => $project->id]) }}">Click here to create the first task</a>.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Project Progress & Quick Add Task -->
            <div class="col-12 col-lg-4">
                <!-- PROGRESS CARD -->
                <div class="card margin-bottom-20">
                    <div class="card-body">
                        <h4>Overall Completion</h4>
                        <p class="subtitle margin-bottom-20">Based on finished project tasks</p>
                        
                        <div class="text-center margin-bottom-20">
                            <h2 class="display-4 font-weight-bold text-primary margin-bottom-0">{{ $project->progress_percentage }}%</h2>
                            <small class="text-muted">Completion Rate</small>
                        </div>

                        <div class="progress margin-bottom-20" style="height: 12px;">
                            <div class="progress-bar {{ $project->progress_percentage == 100 ? 'bg-success' : 'bg-primary' }}" 
                                 role="progressbar" 
                                 style="width: {{ $project->progress_percentage }}%"></div>
                        </div>

                        <div class="d-flex justify-content-between small text-muted border-top pt-2">
                            <span>Completed: <strong>{{ $project->tasks->where('status', 'completed')->count() }}</strong></span>
                            <span>Pending: <strong>{{ $project->tasks->where('status', '!=', 'completed')->count() }}</strong></span>
                            <span>Total: <strong>{{ $project->tasks->count() }}</strong></span>
                        </div>
                    </div>
                </div>

                <!-- QUICK ADD TASK FORM -->
                <div class="card margin-bottom-20">
                    <div class="card-body">
                        <h4>Quick Add Task</h4>
                        <p class="subtitle margin-bottom-20">Directly add a new work item to this project</p>
                        
                        <form action="{{ route('tasks.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="project_id" value="{{ $project->id }}">
                            <input type="hidden" name="redirect_to_project" value="1">

                            <div class="form-group margin-bottom-15">
                                <label for="quick_title" class="small font-weight-bold">Task Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="quick_title" class="form-control form-control-sm" placeholder="Task name..." required>
                            </div>

                            <div class="form-group margin-bottom-15">
                                <label for="quick_assigned" class="small font-weight-bold">Assign To <span class="text-danger">*</span></label>
                                <select name="assigned_to" id="quick_assigned" class="form-control form-control-sm" required>
                                    @foreach($allUsers as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-row margin-bottom-15">
                                <div class="col-6">
                                    <label for="quick_priority" class="small font-weight-bold">Priority</label>
                                    <select name="priority" id="quick_priority" class="form-control form-control-sm">
                                        <option value="low">Low</option>
                                        <option value="medium" selected>Medium</option>
                                        <option value="high">High</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label for="quick_status" class="small font-weight-bold">Status</label>
                                    <select name="status" id="quick_status" class="form-control form-control-sm">
                                        <option value="pending" selected>Pending</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group margin-bottom-20">
                                <label for="quick_due_date" class="small font-weight-bold">Due Date</label>
                                <input type="date" name="due_date" id="quick_due_date" class="form-control form-control-sm" value="{{ now()->addDays(7)->format('Y-m-d') }}">
                            </div>

                            <button type="submit" class="btn btn-primary btn-block btn-sm">
                                <i class="fa fa-plus margin-right-5"></i> Add Task
                            </button>
                        </form>
                    </div>
                </div>

                <!-- PROJECT ACTIVITY HISTORY -->
                <div class="card margin-bottom-20">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h4>Activity History</h4>
                            <p class="subtitle margin-bottom-0">Recent events on this project</p>
                        </div>
                        <span class="badge badge-pill badge-primary">{{ $project->activities->count() }}</span>
                    </div>
                    <div class="card-body padding-top-0">
                        <ul class="list-group list-group-flush">
                            @forelse($project->activities->take(8) as $activity)
                                <li class="list-group-item px-0 py-2 border-bottom">
                                    <div class="d-flex align-items-start">
                                        <div class="margin-right-10 margin-top-5">
                                            <span class="badge {{ $activity->action_badge_class }} p-2 rounded-circle" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                                                <i class="{{ $activity->action_icon }}" style="font-size: 0.8rem;"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="small font-weight-500 text-dark" style="line-height: 1.35;">
                                                {{ $activity->description }}
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center margin-top-5">
                                                <small class="text-muted">
                                                    @if($activity->task)
                                                        <a href="{{ route('tasks.show', $activity->task) }}" class="text-primary font-weight-bold">
                                                            #TSK-{{ str_pad($activity->task->id, 4, '0', STR_PAD_LEFT) }}
                                                        </a>
                                                    @endif
                                                </small>
                                                <small class="text-muted" title="{{ $activity->created_at->format('M d, Y h:i A') }}">
                                                    {{ $activity->created_at->diffForHumans() }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item px-0 text-center text-muted py-3">
                                    <i class="fa fa-history fa-2x text-muted opacity-50 mb-2"></i>
                                    <p class="mb-0 small">No activity recorded for this project yet.</p>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
