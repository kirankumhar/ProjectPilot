<x-layouts.app title="Dashboard - ProjectPilot">
    <!-- PAGE HEADING -->
    <div class="page-heading">
        <div class="page-heading__container">
            <h1 class="title">ProjectPilot Dashboard</h1>
            <p class="caption">Overview of active projects, task progress, and team statistics</p>
        </div>
        <div class="page-heading__container float-right d-none d-sm-block">
            <a href="{{ route('projects.create') }}" class="btn btn-primary margin-right-5">
                <i class="fa fa-plus-circle margin-right-5"></i> New Project
            </a>
            <a href="{{ route('tasks.create') }}" class="btn btn-success">
                <i class="fa fa-tasks margin-right-5"></i> Add Task
            </a>
        </div>
    </div>
    <!-- //END PAGE HEADING -->

    <div class="container-fluid">
        <!-- METRIC WIDGETS -->
        <div class="form-row margin-bottom-20">
            <div class="col-12 col-sm-6 col-lg-3 margin-bottom-10">
                <div class="widget widget--invert-by-parent">
                    <div class="widget__icon_layer widget__icon_layer--right"><span class="li-briefcase"></span></div>
                    <div class="widget__container">
                        <div class="widget__line">
                            <div class="widget__icon"><span class="li-briefcase"></span></div>
                            <div class="widget__title">Total Projects</div>
                            <div class="widget__subtitle">System wide</div>
                        </div>
                        <div class="widget__box widget__box--left">
                            <div class="widget__informer"><span class="text-bold">{{ $totalProjects }}</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3 margin-bottom-10">
                <div class="widget widget--invert-by-parent">
                    <div class="widget__icon_layer widget__icon_layer--right"><span class="li-rocket"></span></div>
                    <div class="widget__container">
                        <div class="widget__line">
                            <div class="widget__icon"><span class="li-rocket"></span></div>
                            <div class="widget__title">Active Projects</div>
                            <div class="widget__subtitle">In Progress</div>
                        </div>
                        <div class="widget__box widget__box--left">
                            <div class="widget__informer"><span class="text-bold text-primary">{{ $activeProjects }}</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3 margin-bottom-10">
                <div class="widget widget--invert-by-parent">
                    <div class="widget__icon_layer widget__icon_layer--right"><span class="li-check"></span></div>
                    <div class="widget__container">
                        <div class="widget__line">
                            <div class="widget__icon"><span class="li-check"></span></div>
                            <div class="widget__title">Tasks Completed</div>
                            <div class="widget__subtitle">{{ $completedTasks }} of {{ $totalTasks }} total</div>
                        </div>
                        <div class="widget__box widget__box--left">
                            <div class="widget__informer"><span class="text-bold text-success">{{ $completedTasks }}</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3 margin-bottom-10">
                <div class="widget widget--invert-by-parent">
                    <div class="widget__icon_layer widget__icon_layer--right"><span class="li-users2"></span></div>
                    <div class="widget__container">
                        <div class="widget__line">
                            <div class="widget__icon"><span class="li-users2"></span></div>
                            <div class="widget__title">Team Members</div>
                            <div class="widget__subtitle">Active contributors</div>
                        </div>
                        <div class="widget__box widget__box--left">
                            <div class="widget__informer"><span class="text-bold">{{ $totalMembers }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- //END METRIC WIDGETS -->

        <div class="form-row">
            <!-- RECENT PROJECTS TABLE -->
            <div class="col-12 col-xl-8">
                <div class="card margin-bottom-20">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h4>Active Projects Overview</h4>
                            <p class="subtitle margin-bottom-0">List of recently updated projects</p>
                        </div>
                        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary btn-sm">View All</a>
                    </div>
                    <div class="card-body padding-top-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered margin-bottom-0">
                                <thead>
                                    <tr>
                                        <th>Project Name</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th width="160">Progress</th>
                                        <th>Due Date</th>
                                        <th width="80">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentProjects as $project)
                                        <tr>
                                            <td>
                                                <a href="{{ route('projects.show', $project) }}" class="text-bold text-dark">
                                                    {{ $project->name }}
                                                </a>
                                                <div class="small text-muted">{{ Str::limit($project->description, 45) }}</div>
                                            </td>
                                            <td>
                                                @if($project->status === 'in_progress')
                                                    <span class="badge badge-primary">In Progress</span>
                                                @elseif($project->status === 'completed')
                                                    <span class="badge badge-success">Completed</span>
                                                @elseif($project->status === 'on_hold')
                                                    <span class="badge badge-warning">On Hold</span>
                                                @else
                                                    <span class="badge badge-secondary">Pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($project->priority === 'high')
                                                    <span class="badge badge-danger">High</span>
                                                @elseif($project->priority === 'medium')
                                                    <span class="badge badge-info">Medium</span>
                                                @else
                                                    <span class="badge badge-light">Low</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1 margin-right-10" style="height: 8px;">
                                                        <div class="progress-bar {{ $project->progress_percentage == 100 ? 'bg-success' : 'bg-primary' }}" 
                                                             role="progressbar" 
                                                             style="width: {{ $project->progress_percentage }}%"></div>
                                                    </div>
                                                    <small class="text-bold">{{ $project->progress_percentage }}%</small>
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <i class="fa fa-calendar margin-right-5"></i>
                                                    {{ $project->due_date ? \Carbon\Carbon::parse($project->due_date)->format('M d, Y') : 'N/A' }}
                                                </small>
                                            </td>
                                            <td>
                                                <a href="{{ route('projects.show', $project) }}" class="btn btn-light btn-sm btn-icon" title="View Project">
                                                    <span class="fa fa-eye"></span>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">No projects found. <a href="{{ route('projects.create') }}">Create one now</a>.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RECENT TASKS PANEL -->
            <div class="col-12 col-xl-4">
                <div class="card margin-bottom-20">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h4>Recent Tasks</h4>
                            <p class="subtitle margin-bottom-0">Latest assigned items</p>
                        </div>
                        <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary btn-sm">View All</a>
                    </div>
                    <div class="card-body padding-top-0">
                        <ul class="list-group list-group-flush">
                            @forelse($recentTasks as $task)
                                <li class="list-group-item px-0 py-3">
                                    <div class="d-flex justify-content-between align-items-start margin-bottom-5">
                                        <a href="{{ route('tasks.edit', $task) }}" class="text-bold text-dark">
                                            {{ $task->title }}
                                        </a>
                                        @if($task->status === 'completed')
                                            <span class="badge badge-success">Done</span>
                                        @elseif($task->status === 'in_progress')
                                            <span class="badge badge-primary">Active</span>
                                        @else
                                            <span class="badge badge-warning">Pending</span>
                                        @endif
                                    </div>
                                    <div class="small text-muted d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fa fa-folder margin-right-5"></i>{{ $task->project->name ?? 'General' }}
                                        </span>
                                        <span>
                                            <i class="fa fa-user margin-right-5"></i>{{ $task->assignee->name ?? 'Unassigned' }}
                                        </span>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item px-0 text-center text-muted">No tasks available.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>