<x-layouts.app :title="$task->title . ' - Task Details - ProjectPilot'">
    <!-- PAGE HEADING -->
    <div class="page-heading">
        <div class="page-heading__container">
            <h1 class="title">{{ $task->title }}</h1>
            <p class="caption">Task ID: #TSK-{{ str_pad($task->id, 4, '0', STR_PAD_LEFT) }} &bull; Project: <a href="{{ route('projects.show', $task->project) }}" class="text-primary font-weight-bold">{{ $task->project->name ?? 'Project' }}</a></p>
        </div>
        <div class="page-heading__container float-right d-none d-sm-block">
            <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary margin-right-5">
                <i class="fa fa-arrow-left margin-right-5"></i> Back to Board
            </a>
            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-primary margin-right-5">
                <i class="fa fa-pencil margin-right-5"></i> Edit Task
            </a>
            <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this task?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                    <i class="fa fa-trash"></i> Delete
                </button>
            </form>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projects</a></li>
                <li class="breadcrumb-item"><a href="{{ route('projects.show', $task->project) }}">{{ Str::limit($task->project->name, 20) }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tasks.index') }}">Tasks</a></li>
                <li class="breadcrumb-item active">#TSK-{{ str_pad($task->id, 4, '0', STR_PAD_LEFT) }}</li>
            </ol>
        </nav>
    </div>
    <!-- //END PAGE HEADING -->

    <div class="container-fluid">
        <div class="row">
            <!-- LEFT COLUMN: Task Details & Comments Feed -->
            <div class="col-12 col-lg-8">
                <!-- TASK OVERVIEW CARD -->
                <div class="card margin-bottom-20 shadow-sm border">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge {{ $task->type_badge_class }} mr-2">
                                {{ $task->type_display }}
                            </span>
                            @if($task->status === 'completed')
                                <span class="badge badge-success mr-2">Completed</span>
                            @elseif($task->status === 'in_progress')
                                <span class="badge badge-primary mr-2">In Progress</span>
                            @else
                                <span class="badge badge-secondary mr-2">Pending</span>
                            @endif

                            @if($task->priority === 'high')
                                <span class="badge badge-danger">High Priority</span>
                            @elseif($task->priority === 'medium')
                                <span class="badge badge-info">Medium Priority</span>
                            @else
                                <span class="badge badge-light">Low Priority</span>
                            @endif
                        </div>
                        <div class="text-muted small">
                            <i class="fa fa-clock-o margin-right-5"></i> Created {{ $task->created_at->format('M d, Y') }}
                        </div>
                    </div>

                    <div class="card-body">
                        <h4 class="mb-3">{{ $task->title }}</h4>

                        <div class="p-3 bg-light rounded border mb-3">
                            <h6 class="text-muted text-uppercase small font-weight-bold mb-2">Description</h6>
                            @if(!empty(trim($task->description)))
                                <div class="text-dark" style="white-space: pre-line; line-height: 1.6;">{{ $task->description }}</div>
                            @else
                                <p class="text-muted font-italic mb-0">No description provided for this task.</p>
                            @endif
                        </div>

                        <!-- TASK MAIN ATTACHMENT -->
                        @if($task->attachment)
                            <div class="p-3 bg-white rounded border d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="p-2 bg-light rounded text-primary mr-3" style="font-size: 1.5rem;">
                                        <i class="fa fa-paperclip"></i>
                                    </div>
                                    <div>
                                        <strong>Task Attachment:</strong>
                                        <div class="text-muted small">{{ $task->attachment_name }}</div>
                                    </div>
                                </div>
                                <a href="{{ $task->attachment_url }}" target="_blank" class="btn btn-sm btn-outline-primary" download>
                                    <i class="fa fa-download margin-right-5"></i> Download File
                                </a>
                            </div>
                        @endif
                <!-- CHECKLIST & SUBTASKS SECTION -->
                <div class="card margin-bottom-20 shadow-sm border" id="checklist-card">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-check-square-o text-success margin-right-10" style="font-size: 1.25rem;"></i>
                            <h5 class="mb-0 font-weight-bold">Subtasks & Checklist</h5>
                            <span class="badge badge-pill badge-info ml-2" id="checklist-counter-badge">
                                {{ $task->checklist_completed_count }}/{{ $task->checklist_total_count }}
                            </span>
                        </div>
                        <div>
                            <span class="text-muted small font-weight-bold mr-1" id="checklist-pct-label">{{ $task->checklist_progress_percentage }}% Completed</span>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <!-- PROGRESS BAR -->
                        <div class="progress mb-4" style="height: 8px; border-radius: 4px; background-color: #e9ecef;">
                            <div class="progress-bar {{ $task->checklist_progress_percentage == 100 ? 'bg-success' : 'bg-primary' }}" 
                                 id="checklist-progress-bar"
                                 role="progressbar" 
                                 style="width: {{ $task->checklist_progress_percentage }}%; transition: width 0.4s ease;" 
                                 aria-valuenow="{{ $task->checklist_progress_percentage }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>

                        <!-- CHECKLIST ITEMS LIST -->
                        <div id="checklist-items-container" class="margin-bottom-20">
                            @forelse($task->checklists as $item)
                                <div class="checklist-item d-flex align-items-center justify-content-between p-2 mb-2 rounded border bg-light {{ $item->is_completed ? 'item-completed' : '' }}" 
                                     id="checklist-item-{{ $item->id }}"
                                     data-id="{{ $item->id }}">
                                    <div class="d-flex align-items-center flex-grow-1 mr-3">
                                        <div class="custom-control custom-checkbox mr-3">
                                            <input type="checkbox" 
                                                   class="custom-control-input checklist-toggle" 
                                                   id="chk-{{ $item->id }}" 
                                                   data-id="{{ $item->id }}"
                                                   {{ $item->is_completed ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="chk-{{ $item->id }}"></label>
                                        </div>
                                        <div>
                                            <span class="checklist-title {{ $item->is_completed ? 'text-muted text-strikethrough' : 'text-dark font-weight-500' }}" 
                                                  id="chk-title-{{ $item->id }}">
                                                {{ $item->title }}
                                            </span>
                                            <div class="checklist-meta text-muted small" id="chk-meta-{{ $item->id }}">
                                                @if($item->is_completed && $item->completedBy)
                                                    <i class="fa fa-check text-success mr-1"></i> Completed by {{ $item->completedBy->name }} {{ $item->completed_at?->diffForHumans() }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <button type="button" 
                                                class="btn btn-sm btn-link text-danger p-1 delete-checklist-btn" 
                                                data-id="{{ $item->id }}"
                                                title="Delete Subtask">
                                            <i class="fa fa-trash-o"></i>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div id="checklist-empty-state" class="text-center py-3 text-muted">
                                    <i class="fa fa-tasks fa-2x mb-2 text-muted opacity-50"></i>
                                    <p class="mb-0 small">No subtasks yet. Break down this task into smaller steps below.</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- ADD CHECKLIST ITEM FORM -->
                        <form id="add-checklist-form" action="{{ route('tasks.checklists.store', $task) }}" method="POST">
                            @csrf
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white border-right-0"><i class="fa fa-plus text-primary"></i></span>
                                </div>
                                <input type="text" 
                                       name="title" 
                                       id="new-checklist-input" 
                                       class="form-control border-left-0" 
                                       placeholder="Add a new subtask or checklist item..." 
                                       required 
                                       maxlength="255">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary px-3" id="add-checklist-btn">
                                        Add Subtask
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- DISCUSSION & COMMENTS THREAD -->
                <div class="card margin-bottom-20 shadow-sm border" id="comments-section">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                        <h5 class="mb-0 font-weight-bold d-flex align-items-center">
                            <i class="fa fa-comments text-primary margin-right-10"></i> Discussion & Comments
                            <span class="badge badge-pill badge-primary ml-2">{{ $task->comments->count() }}</span>
                        </h5>
                        <span class="text-muted small">Team Collaboration Feed</span>
                    </div>

                    <div class="card-body p-4 bg-light">
                        <!-- COMMENTS LIST -->
                        <div class="comments-stream">
                            @forelse($task->comments as $comment)
                                <div class="card mb-3 border shadow-xs" id="comment-{{ $comment->id }}">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $comment->user->avatar_url ?? asset('assets/img/users/user_1.jpg') }}" 
                                                     alt="{{ $comment->user->name ?? 'User' }}" 
                                                     class="rounded-circle mr-2 border" 
                                                     style="width: 38px; height: 38px; object-fit: cover;">
                                                <div>
                                                    <strong class="text-dark">{{ $comment->user->name ?? 'User' }}</strong>
                                                    @if($comment->user)
                                                        <span class="badge badge-light ml-1">{{ $comment->user->role_display }}</span>
                                                    @endif
                                                    <div class="text-muted small" title="{{ $comment->created_at->format('Y-m-d H:i:s') }}">
                                                        <i class="fa fa-clock-o mr-1"></i> {{ $comment->created_at->diffForHumans() }}
                                                    </div>
                                                </div>
                                            </div>

                                            @if(auth()->id() === $comment->user_id || (auth()->check() && auth()->user()->isAdmin()))
                                                <form action="{{ route('tasks.comments.destroy', $comment) }}" method="POST" onsubmit="return confirm('Delete this comment?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0" title="Delete comment">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>

                                        <!-- COMMENT BODY -->
                                        <div class="comment-text text-dark pl-1 mb-2" style="white-space: pre-line; line-height: 1.55;">{{ $comment->comment }}</div>

                                        <!-- COMMENT ATTACHMENT -->
                                        @if($comment->attachment)
                                            <div class="mt-2 pt-2 border-top">
                                                @if($comment->is_image)
                                                    <div class="mb-2">
                                                        <a href="{{ $comment->attachment_url }}" target="_blank">
                                                            <img src="{{ $comment->attachment_url }}" alt="Attachment" class="img-thumbnail rounded" style="max-height: 180px; max-width: 100%; object-fit: cover;">
                                                        </a>
                                                    </div>
                                                @endif
                                                <a href="{{ $comment->attachment_url }}" target="_blank" class="btn btn-sm btn-light border" download>
                                                    <i class="fa fa-paperclip text-primary mr-1"></i> {{ $comment->attachment_name }}
                                                    <span class="text-muted ml-1"><i class="fa fa-download"></i></span>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted bg-white rounded border">
                                    <i class="fa fa-comment-o fa-3x text-muted mb-2"></i>
                                    <h6>No comments yet</h6>
                                    <p class="small text-muted mb-0">Be the first to share an update, discuss requirements, or ask a question on this task.</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- POST COMMENT FORM -->
                        <div class="card mt-4 border shadow-sm">
                            <div class="card-header bg-white py-2 font-weight-bold text-dark">
                                <i class="fa fa-pencil text-primary mr-1"></i> Leave a Comment
                            </div>
                            <div class="card-body">
                                <form action="{{ route('tasks.comments.store', $task) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <textarea name="comment" 
                                                  id="comment-input" 
                                                  rows="3" 
                                                  class="form-control @error('comment') is-invalid @enderror" 
                                                  placeholder="Write your comment, update, or feedback..." 
                                                  required>{{ old('comment') }}</textarea>
                                        @error('comment')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                                        <div class="form-group mb-0 mr-2">
                                            <label for="comment-attachment" class="btn btn-sm btn-outline-secondary mb-0 cursor-pointer">
                                                <i class="fa fa-paperclip mr-1"></i> Attach File / Screenshot
                                            </label>
                                            <input type="file" name="attachment" id="comment-attachment" class="d-none" onchange="document.getElementById('attachment-file-name').textContent = this.files[0] ? this.files[0].name : '';">
                                            <span id="attachment-file-name" class="ml-2 small text-muted font-italic"></span>
                                        </div>

                                        <button type="submit" class="btn btn-primary px-4">
                                            <i class="fa fa-paper-plane mr-1"></i> Post Comment
                                        </button>
                                    </div>
                                    @error('attachment')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Metadata, Project, Assignee & Due Date -->
            <div class="col-12 col-lg-4">
                <!-- PROJECT INFO CARD -->
                <div class="card margin-bottom-20 shadow-sm border">
                    <div class="card-header bg-white py-3 font-weight-bold">
                        <i class="fa fa-briefcase text-primary mr-1"></i> Project Details
                    </div>
                    <div class="card-body">
                        <h5>
                            <a href="{{ route('projects.show', $task->project) }}" class="text-dark">
                                {{ $task->project->name }}
                            </a>
                        </h5>
                        <p class="text-muted small mb-3">{{ Str::limit($task->project->description, 100) }}</p>

                        <div class="mb-2">
                            <span class="badge {{ $task->project->type_badge_class }}">
                                <i class="fa {{ $task->project->isMaintenance() ? 'fa-wrench' : 'fa-rocket' }} mr-1"></i> {{ $task->project->type_display }}
                            </span>
                            <span class="badge badge-light border">Progress: {{ $task->project->progress_percentage }}%</span>
                        </div>

                        <div class="small text-muted border-top pt-2 mt-3">
                            <i class="fa fa-user mr-1"></i> Project Owner: <strong>{{ $task->project->owner->name ?? 'Admin' }}</strong>
                        </div>
                        @if($task->project->url)
                            <div class="small mt-1">
                                <a href="{{ $task->project->url }}" target="_blank" class="text-info">
                                    <i class="fa fa-external-link mr-1"></i> {{ $task->project->url }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- ASSIGNEE CARD -->
                <div class="card margin-bottom-20 shadow-sm border">
                    <div class="card-header bg-white py-3 font-weight-bold">
                        <i class="fa fa-user text-primary mr-1"></i> Assigned To
                    </div>
                    <div class="card-body">
                        @if($task->assignee)
                            <div class="d-flex align-items-center">
                                <img src="{{ $task->assignee->avatar_url ?? asset('assets/img/users/user_1.jpg') }}" 
                                     alt="{{ $task->assignee->name }}" 
                                     class="rounded-circle mr-3 border" 
                                     style="width: 50px; height: 50px; object-fit: cover;">
                                <div>
                                    <h5 class="mb-0 font-weight-bold">{{ $task->assignee->name }}</h5>
                                    <span class="badge badge-primary mt-1">{{ $task->assignee->role_display }}</span>
                                    <div class="text-muted small mt-1">
                                        <i class="fa fa-envelope-o mr-1"></i> {{ $task->assignee->email }}
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-top">
                                <a href="{{ route('chat.index', ['user_id' => $task->assignee->id]) }}" class="btn btn-sm btn-outline-info btn-block">
                                    <i class="fa fa-comment mr-1"></i> Send Direct Message
                                </a>
                            </div>
                        @else
                            <span class="text-muted font-italic">Unassigned</span>
                        @endif
                    </div>
                </div>

                <!-- TIMELINE & DATES CARD -->
                <div class="card margin-bottom-20 shadow-sm border">
                    <div class="card-header bg-white py-3 font-weight-bold">
                        <i class="fa fa-calendar text-primary mr-1"></i> Timeline & Schedule
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted">Start Date:</span>
                                <strong>{{ $task->start_date ? \Carbon\Carbon::parse($task->start_date)->format('M d, Y') : 'Not Set' }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted">Due Date:</span>
                                <div>
                                    <strong>{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('M d, Y') : 'Not Set' }}</strong>
                                    @if($task->due_date && \Carbon\Carbon::parse($task->due_date)->isPast() && $task->status !== 'completed')
                                        <span class="badge badge-danger ml-1">Overdue</span>
                                    @endif
                                </div>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted">Created:</span>
                                <span>{{ $task->created_at->format('M d, Y') }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted">Last Updated:</span>
                                <span>{{ $task->updated_at->diffForHumans() }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- TIME TRACKING & WORK LOGS CARD -->
                <div class="card margin-bottom-20 shadow-sm border" id="time-tracking-card">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <div class="font-weight-bold">
                            <i class="fa fa-clock-o text-primary mr-1"></i> Time Tracking
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#logTimeModal">
                            <i class="fa fa-plus mr-1"></i> Log Time
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- PROGRESS BAR -->
                        <div class="d-flex justify-content-between align-items-center mb-1 small">
                            <span>Logged: <strong class="{{ $task->is_over_budget ? 'text-danger' : 'text-primary' }}">{{ $task->total_logged_hours }} hrs</strong></span>
                            <span>Estimate: <strong>{{ $task->estimated_hours > 0 ? $task->estimated_hours . ' hrs' : 'Not set' }}</strong></span>
                        </div>

                        @if($task->estimated_hours > 0)
                            <div class="progress mb-2" style="height: 10px;">
                                <div class="progress-bar {{ $task->is_over_budget ? 'bg-danger' : 'bg-primary' }}" 
                                     role="progressbar" 
                                     style="width: {{ $task->time_progress_percentage }}%"></div>
                            </div>
                            @if($task->is_over_budget)
                                <div class="text-danger small font-weight-bold mb-3">
                                    <i class="fa fa-exclamation-triangle mr-1"></i> Over budget by {{ round($task->total_logged_hours - $task->estimated_hours, 2) }} hrs
                                </div>
                            @endif
                        @else
                            <div class="text-muted small font-italic mb-3">No hour estimate set for this task.</div>
                        @endif

                        <!-- RECENT TIME LOGS LIST -->
                        <div class="border-top pt-2">
                            <h6 class="small font-weight-bold text-muted text-uppercase mb-2">Work Logs</h6>
                            <ul class="list-group list-group-flush small">
                                @forelse($task->timeLogs as $log)
                                    <li class="list-group-item px-0 py-2">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong class="text-dark">{{ $log->hours }} hrs</strong>
                                                <span class="text-muted">&bull; {{ $log->user->name ?? 'User' }}</span>
                                                <div class="text-muted small">{{ \Carbon\Carbon::parse($log->logged_date)->format('M d, Y') }}</div>
                                                @if($log->note)
                                                    <div class="text-muted font-italic mt-1" style="font-size: 0.78rem;">"{{ $log->note }}"</div>
                                                @endif
                                            </div>
                                            @if(auth()->id() === $log->user_id || (auth()->check() && auth()->user()->isAdmin()))
                                                <form action="{{ route('tasks.time-logs.destroy', $log) }}" method="POST" onsubmit="return confirm('Delete this work log?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0 ml-2" title="Delete work log">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </li>
                                @empty
                                    <li class="list-group-item px-0 py-2 text-muted font-italic text-center">
                                        No hours logged yet.
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- TASK ACTIVITY AUDIT HISTORY -->
                <div class="card margin-bottom-20 shadow-sm border">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <div class="font-weight-bold">
                            <i class="fa fa-history text-primary mr-1"></i> Activity History
                        </div>
                        <span class="badge badge-pill badge-primary">{{ $task->activities->count() }}</span>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush small">
                            @forelse($task->activities->take(6) as $activity)
                                <li class="list-group-item px-3 py-2 border-bottom">
                                    <div class="d-flex align-items-start">
                                        <div class="margin-right-10 margin-top-5">
                                            <span class="badge {{ $activity->action_badge_class }} p-1 rounded-circle" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;">
                                                <i class="{{ $activity->action_icon }}" style="font-size: 0.75rem;"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="font-weight-500 text-dark" style="line-height: 1.35;">
                                                {{ $activity->description }}
                                            </div>
                                            <div class="text-muted mt-1" style="font-size: 0.75rem;" title="{{ $activity->created_at->format('M d, Y h:i A') }}">
                                                {{ $activity->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item text-center text-muted py-3">
                                    No activity logs for this task yet.
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- LOG TIME MODAL -->
    <div class="modal fade" id="logTimeModal" tabindex="-1" role="dialog" aria-labelledby="logTimeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <form action="{{ route('tasks.time-logs.store', $task) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-light">
                        <h5 class="modal-title font-weight-bold text-dark" id="logTimeModalLabel">
                            <i class="fa fa-clock-o text-primary mr-1"></i> Log Work Hours
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="form-group mb-3">
                            <label for="modal-hours" class="font-weight-bold small">Time Spent (Hours) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.25" min="0.1" max="24" name="hours" id="modal-hours" class="form-control" placeholder="e.g. 2.5" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">Hours</span>
                                </div>
                            </div>
                            <small class="text-muted">Enter hours (e.g. 1.5 = 1 hour 30 mins)</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="modal-logged-date" class="font-weight-bold small">Date of Work <span class="text-danger">*</span></label>
                            <input type="date" name="logged_date" id="modal-logged-date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                        </div>

                        <div class="form-group mb-0">
                            <label for="modal-note" class="font-weight-bold small">Work Description / Note (Optional)</label>
                            <textarea name="note" id="modal-note" rows="3" class="form-control" placeholder="What did you work on?"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-white">
                        <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm px-4">
                            <i class="fa fa-save mr-1"></i> Save Work Log
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .text-strikethrough {
            text-decoration: line-through;
            opacity: 0.6;
        }
        .checklist-item {
            transition: all 0.2s ease-in-out;
        }
        .checklist-item:hover {
            background-color: #f1f5f9 !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = '{{ csrf_token() }}';

            function updateProgressUI(stats) {
                if (!stats) return;
                const pct = stats.percentage || 0;
                const total = stats.total || 0;
                const completed = stats.completed || 0;

                const progressBar = document.getElementById('checklist-progress-bar');
                const counterBadge = document.getElementById('checklist-counter-badge');
                const pctLabel = document.getElementById('checklist-pct-label');

                if (progressBar) {
                    progressBar.style.width = pct + '%';
                    progressBar.setAttribute('aria-valuenow', pct);
                    if (pct === 100) {
                        progressBar.classList.remove('bg-primary');
                        progressBar.classList.add('bg-success');
                    } else {
                        progressBar.classList.remove('bg-success');
                        progressBar.classList.add('bg-primary');
                    }
                }

                if (counterBadge) {
                    counterBadge.textContent = `${completed}/${total}`;
                }

                if (pctLabel) {
                    pctLabel.textContent = `${pct}% Completed`;
                }
            }

            // TOGGLE CHECKBOX
            document.addEventListener('change', function (e) {
                if (e.target && e.target.classList.contains('checklist-toggle')) {
                    const id = e.target.getAttribute('data-id');
                    const isChecked = e.target.checked;
                    const titleSpan = document.getElementById('chk-title-' + id);
                    const metaDiv = document.getElementById('chk-meta-' + id);

                    if (isChecked) {
                        titleSpan.classList.add('text-muted', 'text-strikethrough');
                        titleSpan.classList.remove('font-weight-500', 'text-dark');
                    } else {
                        titleSpan.classList.remove('text-muted', 'text-strikethrough');
                        titleSpan.classList.add('font-weight-500', 'text-dark');
                    }

                    fetch(`/checklists/${id}/toggle`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            if (data.is_completed) {
                                metaDiv.innerHTML = `<i class="fa fa-check text-success mr-1"></i> Completed by ${data.completed_by_name || 'You'} just now`;
                            } else {
                                metaDiv.innerHTML = '';
                            }
                            updateProgressUI(data.stats);
                        }
                    })
                    .catch(err => {
                        console.error('Checklist toggle error:', err);
                    });
                }
            });

            // ADD CHECKLIST FORM
            const addForm = document.getElementById('add-checklist-form');
            if (addForm) {
                addForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const input = document.getElementById('new-checklist-input');
                    const title = input.value.trim();
                    if (!title) return;

                    const addBtn = document.getElementById('add-checklist-btn');
                    addBtn.disabled = true;

                    fetch(addForm.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ title: title })
                    })
                    .then(res => res.json())
                    .then(data => {
                        addBtn.disabled = false;
                        if (data.success && data.checklist) {
                            input.value = '';
                            const emptyState = document.getElementById('checklist-empty-state');
                            if (emptyState) emptyState.remove();

                            const item = data.checklist;
                            const container = document.getElementById('checklist-items-container');

                            const newItemHtml = `
                                <div class="checklist-item d-flex align-items-center justify-content-between p-2 mb-2 rounded border bg-light" 
                                     id="checklist-item-${item.id}" 
                                     data-id="${item.id}">
                                    <div class="d-flex align-items-center flex-grow-1 mr-3">
                                        <div class="custom-control custom-checkbox mr-3">
                                            <input type="checkbox" 
                                                   class="custom-control-input checklist-toggle" 
                                                   id="chk-${item.id}" 
                                                   data-id="${item.id}">
                                            <label class="custom-control-label" for="chk-${item.id}"></label>
                                        </div>
                                        <div>
                                            <span class="checklist-title text-dark font-weight-500" 
                                                  id="chk-title-${item.id}">
                                                ${item.title}
                                            </span>
                                            <div class="checklist-meta text-muted small" id="chk-meta-${item.id}"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <button type="button" 
                                                class="btn btn-sm btn-link text-danger p-1 delete-checklist-btn" 
                                                data-id="${item.id}"
                                                title="Delete Subtask">
                                            <i class="fa fa-trash-o"></i>
                                        </button>
                                    </div>
                                </div>
                            `;

                            container.insertAdjacentHTML('beforeend', newItemHtml);
                            updateProgressUI(data.stats);
                        }
                    })
                    .catch(err => {
                        addBtn.disabled = false;
                        console.error('Checklist add error:', err);
                    });
                });
            }

            // DELETE CHECKLIST ITEM
            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.delete-checklist-btn');
                if (btn) {
                    const id = btn.getAttribute('data-id');
                    if (!id) return;

                    fetch(`/checklists/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const itemEl = document.getElementById('checklist-item-' + id);
                            if (itemEl) {
                                itemEl.remove();
                            }
                            updateProgressUI(data.stats);

                            const container = document.getElementById('checklist-items-container');
                            if (container && container.children.length === 0) {
                                container.innerHTML = `
                                    <div id="checklist-empty-state" class="text-center py-3 text-muted">
                                        <i class="fa fa-tasks fa-2x mb-2 text-muted opacity-50"></i>
                                        <p class="mb-0 small">No subtasks yet. Break down this task into smaller steps below.</p>
                                    </div>
                                `;
                            }
                        }
                    })
                    .catch(err => {
                        console.error('Checklist delete error:', err);
                    });
                }
            });
        });
    </script>
</x-layouts.app>
