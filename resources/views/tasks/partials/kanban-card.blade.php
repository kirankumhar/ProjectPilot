<div class="kanban-card priority-{{ $task->priority }}" draggable="true" data-task-id="{{ $task->id }}">
    <div class="d-flex align-items-center justify-content-between margin-bottom-10">
        <div class="d-flex align-items-center gap-1">
            <span class="badge {{ $task->type_badge_class }}" style="font-size: 0.75rem;">
                {{ $task->type_display }}
            </span>
            @if($task->project)
                <a href="{{ route('projects.show', $task->project) }}" class="badge badge-light text-dark border ml-1" title="Project: {{ $task->project->name }}">
                    <i class="fa fa-folder text-primary margin-right-5"></i>{{ Str::limit($task->project->name, 16) }}
                </a>
            @endif
        </div>
        <small class="text-muted font-weight-bold" style="font-size: 0.75rem;">#TSK-{{ str_pad($task->id, 4, '0', STR_PAD_LEFT) }}</small>
    </div>

    <a href="{{ route('tasks.edit', $task) }}" class="task-title">
        {{ $task->title }}
    </a>

    @if($task->description)
        <div class="card-desc">
            {{ Str::limit($task->description, 65) }}
        </div>
    @endif

    <div class="card-meta">
        <div class="d-flex align-items-center gap-1">
            <!-- PRIORITY BADGE -->
            @if($task->priority === 'high')
                <span class="badge badge-danger" title="High Priority">High</span>
            @elseif($task->priority === 'medium')
                <span class="badge badge-info" title="Medium Priority">Med</span>
            @else
                <span class="badge badge-light text-muted border" title="Low Priority">Low</span>
            @endif

            <!-- DUE DATE BADGE -->
            @if($task->due_date)
                @php
                    $isOverdue = \Carbon\Carbon::parse($task->due_date)->isPast() && $task->status !== 'completed';
                @endphp
                <span class="badge {{ $isOverdue ? 'badge-danger' : 'badge-light text-muted border' }}" title="Due Date: {{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}">
                    <i class="fa fa-clock-o margin-right-5"></i>{{ \Carbon\Carbon::parse($task->due_date)->format('M d') }}
                </span>
            @endif

            <!-- ATTACHMENT BADGE -->
            @if($task->attachment)
                <a href="{{ $task->attachment_url }}" target="_blank" class="badge badge-info" title="Attachment: {{ $task->attachment_name }}">
                    <i class="fa fa-paperclip"></i>
                </a>
            @endif
        </div>

        <div class="d-flex align-items-center">
            <!-- ASSIGNEE AVATAR -->
            <img src="{{ $task->assignee ? $task->assignee->avatar_url : asset('assets/img/users/user_1.jpg') }}" alt="Assignee" class="rounded-circle margin-right-5" style="width: 26px; height: 26px; object-fit: cover;" title="Assigned to: {{ $task->assignee->name ?? 'Unassigned' }}">
            
            <!-- ACTIONS DROPDOWN -->
            <div class="dropdown">
                <button class="btn btn-link text-muted p-0 margin-left-5 d-inline-flex align-items-center justify-content-center" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 24px; height: 24px; text-decoration: none;" title="Options">
                    <i class="fa fa-ellipsis-v" style="float: none; margin: 0; line-height: 1; font-size: 14px;"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="{{ route('tasks.edit', $task) }}">
                        <i class="fa fa-pencil text-primary margin-right-5"></i> Edit Task
                    </a>
                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this task?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="dropdown-item text-danger" style="cursor: pointer;">
                            <i class="fa fa-trash margin-right-5"></i> Delete Task
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
