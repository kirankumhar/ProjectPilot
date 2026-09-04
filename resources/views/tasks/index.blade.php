<x-layouts.app title="Tasks Board - ProjectPilot">
    <style>
        .kanban-board {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            padding-bottom: 25px;
            align-items: flex-start;
        }
        .kanban-col-container {
            flex: 1;
            min-width: 320px;
            max-width: 400px;
            background: #f8fafc;
            border-radius: 10px;
            padding: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .kanban-col-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
        }
        .kanban-col-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .kanban-column {
            min-height: 480px;
            border-radius: 8px;
            transition: background-color 0.2s ease, border 0.2s ease;
            padding: 4px;
        }
        .kanban-column.drag-over {
            background-color: #f0f9ff !important;
            border: 2px dashed #0284c7 !important;
        }
        .kanban-card {
            background: #ffffff;
            border-radius: 8px;
            padding: 14px;
            margin-bottom: 14px;
            cursor: grab;
            transition: transform 0.15s ease, box-shadow 0.15s ease, opacity 0.15s ease;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
            border-left-width: 5px !important;
            user-select: none;
        }
        .kanban-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.1) !important;
        }
        .kanban-card.dragging {
            opacity: 0.4;
            cursor: grabbing;
        }
        .kanban-card.priority-high {
            border-left-color: #ef4444 !important;
        }
        .kanban-card.priority-medium {
            border-left-color: #06b6d4 !important;
        }
        .kanban-card.priority-low {
            border-left-color: #94a3b8 !important;
        }
        .kanban-card .task-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: #0f172a;
            line-height: 1.35;
        }
        .kanban-card .task-title:hover {
            color: #0284c7;
            text-decoration: none;
        }
        .kanban-card .card-desc {
            font-size: 0.82rem;
            color: #64748b;
            margin-top: 6px;
            margin-bottom: 10px;
            line-height: 1.4;
        }
        .kanban-card .card-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #f1f5f9;
        }
        .view-switcher .btn {
            padding: 6px 14px;
            font-size: 0.9rem;
        }
        .view-switcher .btn.active {
            background-color: #0284c7;
            color: #ffffff;
            border-color: #0284c7;
        }
    </style>

    <div class="page-heading">
        <div class="page-heading__container">
            <h1 class="title">Tasks Management</h1>
            <p class="caption">Jira-Style Kanban Board & Task Tracking System</p>
        </div>
        <div class="page-heading__container float-right d-flex align-items-center gap-2">
            <!-- VIEW MODE SWITCHER -->
            <div class="btn-group view-switcher margin-right-10">
                <a href="{{ route('tasks.index', array_merge(request()->query(), ['view' => 'board'])) }}" class="btn btn-outline-primary {{ $viewMode === 'board' ? 'active' : '' }}" title="Kanban Board View">
                    <i class="fa fa-th-large margin-right-5"></i> Board
                </a>
                <a href="{{ route('tasks.index', array_merge(request()->query(), ['view' => 'list'])) }}" class="btn btn-outline-primary {{ $viewMode === 'list' ? 'active' : '' }}" title="List Table View">
                    <i class="fa fa-list margin-right-5"></i> List
                </a>
            </div>
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
                    <input type="hidden" name="view" value="{{ $viewMode }}">
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
                        <select name="priority" class="form-control">
                            <option value="">All Priorities</option>
                            <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                            <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3 margin-bottom-10 d-flex">
                        <button type="submit" class="btn btn-secondary btn-block margin-right-5">
                            <i class="fa fa-filter margin-right-5"></i> Filter
                        </button>
                        <a href="{{ route('tasks.index', ['view' => $viewMode]) }}" class="btn btn-light" title="Reset Filters">
                            <i class="fa fa-refresh"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        @if($viewMode === 'board')
            <!-- JIRA KANBAN BOARD VIEW -->
            @php
                $pendingTasks = $allTasksForBoard->where('status', 'pending');
                $inProgressTasks = $allTasksForBoard->where('status', 'in_progress');
                $completedTasks = $allTasksForBoard->where('status', 'completed');
            @endphp

            <div class="kanban-board">
                <!-- COLUMN 1: PENDING -->
                <div class="kanban-col-container">
                    <div class="kanban-col-header">
                        <div class="kanban-col-title">
                            <span class="badge badge-warning" style="width: 10px; height: 10px; border-radius: 50%; padding: 0;"> </span>
                            <span>Pending / To Do</span>
                            <span class="badge badge-pill badge-secondary count-badge margin-left-5">{{ $pendingTasks->count() }}</span>
                        </div>
                        <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-light btn-icon text-muted" title="Quick Add Task">
                            <i class="fa fa-plus"></i>
                        </a>
                    </div>
                    <div class="kanban-column" data-status="pending">
                        <div class="kanban-cards-wrapper">
                            @foreach($pendingTasks as $task)
                                @include('tasks.partials.kanban-card', ['task' => $task])
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- COLUMN 2: IN PROGRESS -->
                <div class="kanban-col-container">
                    <div class="kanban-col-header">
                        <div class="kanban-col-title">
                            <span class="badge badge-primary" style="width: 10px; height: 10px; border-radius: 50%; padding: 0;"> </span>
                            <span>In Progress</span>
                            <span class="badge badge-pill badge-primary count-badge margin-left-5">{{ $inProgressTasks->count() }}</span>
                        </div>
                        <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-light btn-icon text-muted" title="Quick Add Task">
                            <i class="fa fa-plus"></i>
                        </a>
                    </div>
                    <div class="kanban-column" data-status="in_progress">
                        <div class="kanban-cards-wrapper">
                            @foreach($inProgressTasks as $task)
                                @include('tasks.partials.kanban-card', ['task' => $task])
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- COLUMN 3: COMPLETED -->
                <div class="kanban-col-container">
                    <div class="kanban-col-header">
                        <div class="kanban-col-title">
                            <span class="badge badge-success" style="width: 10px; height: 10px; border-radius: 50%; padding: 0;"> </span>
                            <span>Completed / Done</span>
                            <span class="badge badge-pill badge-success count-badge margin-left-5">{{ $completedTasks->count() }}</span>
                        </div>
                        <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-light btn-icon text-muted" title="Quick Add Task">
                            <i class="fa fa-plus"></i>
                        </a>
                    </div>
                    <div class="kanban-column" data-status="completed">
                        <div class="kanban-cards-wrapper">
                            @foreach($completedTasks as $task)
                                @include('tasks.partials.kanban-card', ['task' => $task])
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- TABLE LIST VIEW -->
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
                                            <a href="{{ route('tasks.show', $task) }}" class="font-weight-bold text-dark">
                                                {{ $task->title }}
                                            </a>
                                            @if($task->description)
                                                <div class="small text-muted">{{ Str::limit($task->description, 50) }}</div>
                                            @endif
                                            <div class="mt-1">
                                                @if($task->attachment)
                                                    <a href="{{ $task->attachment_url }}" target="_blank" class="badge badge-info mr-1" title="{{ $task->attachment_name }}">
                                                        <i class="fa fa-paperclip margin-right-5"></i> Attachment
                                                    </a>
                                                @endif
                                                <a href="{{ route('tasks.show', $task) }}#comments-section" class="badge {{ ($task->comments_count ?? 0) > 0 ? 'badge-primary' : 'badge-light text-muted border' }}" title="{{ $task->comments_count ?? 0 }} comments">
                                                    <i class="fa fa-comments margin-right-5"></i>{{ $task->comments_count ?? 0 }}
                                                </a>
                                                @if($task->checklist_total_count > 0)
                                                    <a href="{{ route('tasks.show', $task) }}#checklist-card" class="badge {{ $task->checklist_progress_percentage == 100 ? 'badge-success' : 'badge-light text-muted border' }} ml-1" title="Checklist: {{ $task->checklist_completed_count }}/{{ $task->checklist_total_count }} completed">
                                                        <i class="fa fa-check-square-o margin-right-5"></i>{{ $task->checklist_completed_count }}/{{ $task->checklist_total_count }}
                                                    </a>
                                                @endif
                                            </div>
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
                                                <a href="{{ route('tasks.show', $task) }}" class="btn btn-light btn-sm btn-icon" title="View Details & Comments">
                                                    <i class="fa fa-eye"></i>
                                                </a>
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
        @endif
    </div>

    <!-- DRAG AND DROP JAVASCRIPT -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const columns = document.querySelectorAll('.kanban-column');
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

            let draggedCard = null;

            function initCardEvents(card) {
                card.addEventListener('dragstart', handleDragStart);
                card.addEventListener('dragend', handleDragEnd);
            }

            document.querySelectorAll('.kanban-card').forEach(initCardEvents);

            columns.forEach(column => {
                column.addEventListener('dragover', handleDragOver);
                column.addEventListener('dragenter', handleDragEnter);
                column.addEventListener('dragleave', handleDragLeave);
                column.addEventListener('drop', handleDrop);
            });

            function handleDragStart(e) {
                draggedCard = this;
                this.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', this.dataset.taskId);
            }

            function handleDragEnd() {
                this.classList.remove('dragging');
                columns.forEach(col => col.classList.remove('drag-over'));
                draggedCard = null;
            }

            function handleDragOver(e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
            }

            function handleDragEnter(e) {
                e.preventDefault();
                this.classList.add('drag-over');
            }

            function handleDragLeave(e) {
                if (e.target === this || !this.contains(e.relatedTarget)) {
                    this.classList.remove('drag-over');
                }
            }

            function handleDrop(e) {
                e.preventDefault();
                this.classList.remove('drag-over');

                if (!draggedCard) return;

                const targetColumn = this;
                const targetStatus = targetColumn.dataset.status;
                const wrapper = targetColumn.querySelector('.kanban-cards-wrapper');
                const currentColumn = draggedCard.closest('.kanban-column');
                const originalStatus = currentColumn ? currentColumn.dataset.status : null;

                if (targetStatus === originalStatus) return;

                // Move card visually instantly
                wrapper.appendChild(draggedCard);
                updateColumnCounts();

                // Send AJAX update to backend
                const taskId = draggedCard.dataset.taskId;
                fetch(`/tasks/${taskId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: targetStatus })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('success', data.message || 'Task status updated!');
                    } else {
                        // Rollback on error
                        if (currentColumn) {
                            currentColumn.querySelector('.kanban-cards-wrapper').appendChild(draggedCard);
                            updateColumnCounts();
                        }
                        showToast('danger', 'Could not update task status.');
                    }
                })
                .catch(error => {
                    console.error('Task status update error:', error);
                    if (currentColumn) {
                        currentColumn.querySelector('.kanban-cards-wrapper').appendChild(draggedCard);
                        updateColumnCounts();
                    }
                    showToast('danger', 'Network error while updating status.');
                });
            }

            function updateColumnCounts() {
                columns.forEach(col => {
                    const count = col.querySelectorAll('.kanban-card').length;
                    const container = col.closest('.kanban-col-container');
                    const badge = container ? container.querySelector('.count-badge') : null;
                    if (badge) badge.textContent = count;
                });
            }

            function showToast(type, message) {
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
                alertDiv.style.cssText = 'top: 25px; right: 25px; z-index: 99999; min-width: 280px; box-shadow: 0 8px 24px rgba(0,0,0,0.18); border-radius: 8px;';
                alertDiv.innerHTML = `
                    <strong>${type === 'success' ? '<i class="fa fa-check-circle margin-right-5"></i>' : '<i class="fa fa-exclamation-circle margin-right-5"></i>'}</strong> ${message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                `;
                document.body.appendChild(alertDiv);
                setTimeout(() => {
                    if ($(alertDiv).data('bs.alert')) {
                        $(alertDiv).alert('close');
                    } else {
                        alertDiv.remove();
                    }
                }, 3500);
            }
        });
    </script>
</x-layouts.app>
