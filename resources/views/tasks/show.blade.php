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
            </div>
        </div>
    </div>
</x-layouts.app>
