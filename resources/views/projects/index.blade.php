<x-layouts.app title="Projects - ProjectPilot">
    <div class="page-heading">
        <div class="page-heading__container">
            <h1 class="title">Projects Overview</h1>
            <p class="caption">Manage, track, and collaborate on New Development & Maintenance Projects</p>
        </div>
        <div class="page-heading__container float-right">
            <a href="{{ route('projects.create') }}" class="btn btn-primary">
                <i class="fa fa-plus-circle margin-right-5"></i> Create Project
            </a>
        </div>
    </div>

    <div class="container-fluid">
        <!-- PROJECT TYPE TABS -->
        <div class="nav nav-pills nav-fill mb-3 bg-white p-2 rounded border shadow-xs">
            <a class="nav-item nav-link {{ empty(request('type')) ? 'active bg-primary text-white font-weight-bold' : 'text-dark' }}" href="{{ route('projects.index', array_merge(request()->except('type'), [])) }}">
                <i class="fa fa-th-large margin-right-5"></i> All Projects
                <span class="badge badge-light ml-1">{{ $stats['total'] ?? 0 }}</span>
            </a>
            <a class="nav-item nav-link {{ request('type') === 'new_development' ? 'active bg-primary text-white font-weight-bold' : 'text-dark' }}" href="{{ route('projects.index', array_merge(request()->except('type'), ['type' => 'new_development'])) }}">
                <i class="fa fa-rocket text-success margin-right-5"></i> New Development Projects
                <span class="badge badge-light ml-1">{{ $stats['new_dev'] ?? 0 }}</span>
            </a>
            <a class="nav-item nav-link {{ request('type') === 'maintenance' ? 'active bg-primary text-white font-weight-bold' : 'text-dark' }}" href="{{ route('projects.index', array_merge(request()->except('type'), ['type' => 'maintenance'])) }}">
                <i class="fa fa-wrench text-warning margin-right-5"></i> Maintenance Projects
                <span class="badge badge-light ml-1">{{ $stats['maintenance'] ?? 0 }}</span>
            </a>
        </div>

        <!-- FILTER BAR -->
        <div class="card margin-bottom-20">
            <div class="card-body">
                <form action="{{ route('projects.index') }}" method="GET" class="form-row align-items-center">
                    @if(request('type'))
                        <input type="hidden" name="type" value="{{ request('type') }}">
                    @endif

                    <div class="col-12 col-md-4 margin-bottom-10">
                        <input type="text" name="search" class="form-control" placeholder="Search project name or description..." value="{{ request('search') }}">
                    </div>
                    <div class="col-6 col-md-3 margin-bottom-10">
                        <select name="status" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="on_hold" {{ request('status') === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3 margin-bottom-10">
                        <select name="priority" class="form-control">
                            <option value="">All Priorities</option>
                            <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low Priority</option>
                            <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium Priority</option>
                            <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High Priority</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-2 margin-bottom-10">
                        <button type="submit" class="btn btn-secondary btn-block">
                            <i class="fa fa-filter margin-right-5"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- PROJECTS GRID -->
        <div class="row">
            @forelse($projects as $project)
                <div class="col-12 col-md-6 col-xl-4 margin-bottom-20">
                    <div class="card h-100 shadow-sm border">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start margin-bottom-10">
                                <div>
                                    <span class="badge {{ $project->type_badge_class }} margin-right-5">
                                        <i class="fa {{ $project->isMaintenance() ? 'fa-wrench' : 'fa-rocket' }} margin-right-5"></i>{{ $project->type_display }}
                                    </span>

                                    @if($project->status === 'in_progress')
                                        <span class="badge badge-primary">In Progress</span>
                                    @elseif($project->status === 'completed')
                                        <span class="badge badge-success">Completed</span>
                                    @elseif($project->status === 'on_hold')
                                        <span class="badge badge-warning">On Hold</span>
                                    @else
                                        <span class="badge badge-secondary">Pending</span>
                                    @endif

                                    @if($project->priority === 'high')
                                        <span class="badge badge-danger">High Priority</span>
                                    @elseif($project->priority === 'medium')
                                        <span class="badge badge-info">Medium</span>
                                    @else
                                        <span class="badge badge-light">Low</span>
                                    @endif
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm btn-icon d-inline-flex align-items-center justify-content-center" data-toggle="dropdown" type="button" style="width: 32px; height: 32px; padding: 0; border-radius: 6px;" title="Options">
                                        <i class="fa fa-ellipsis-v text-muted" style="float: none; margin: 0; line-height: 1; font-size: 14px;"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{ route('projects.show', $project) }}" class="dropdown-item">
                                            <i class="fa fa-eye margin-right-5"></i> View Details
                                        </a>
                                        <a href="{{ route('projects.edit', $project) }}" class="dropdown-item">
                                            <i class="fa fa-pencil margin-right-5"></i> Edit
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this project?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fa fa-trash margin-right-5"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <h4 class="card-title margin-bottom-10 d-flex align-items-center justify-content-between">
                                <a href="{{ route('projects.show', $project) }}" class="text-dark">
                                    {{ $project->name }}
                                </a>
                                @if($project->url)
                                    <a href="{{ $project->url }}" target="_blank" class="text-info ml-2" title="Visit Live URL: {{ $project->url }}">
                                        <i class="fa fa-external-link"></i>
                                    </a>
                                @endif
                            </h4>

                            <p class="text-muted flex-grow-1 small margin-bottom-15">
                                {{ Str::limit($project->description, 110, '...') }}
                            </p>

                            <!-- PROGRESS BAR -->
                            <div class="margin-bottom-15">
                                <div class="d-flex justify-content-between small text-muted margin-bottom-5">
                                    <span>Task Progress</span>
                                    <span class="text-bold">{{ $project->progress_percentage }}%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar {{ $project->progress_percentage == 100 ? 'bg-success' : 'bg-primary' }}" 
                                         role="progressbar" 
                                         style="width: {{ $project->progress_percentage }}%"></div>
                                </div>
                            </div>

                            <div class="pt-3 border-top d-flex justify-content-between align-items-center small text-muted">
                                <div>
                                    <i class="fa fa-user margin-right-5"></i> Owner: <strong>{{ $project->owner->name ?? 'Admin' }}</strong>
                                </div>
                                <div>
                                    <i class="fa fa-calendar margin-right-5"></i>
                                    {{ $project->due_date ? \Carbon\Carbon::parse($project->due_date)->format('M d, Y') : 'No Due Date' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card card-body text-center py-5">
                        <i class="fa fa-folder-open-o fa-3x text-muted margin-bottom-15"></i>
                        <h4>No Projects Found</h4>
                        <p class="text-muted">Start by creating your first project to manage team tasks effectively.</p>
                        <div>
                            <a href="{{ route('projects.create') }}" class="btn btn-primary">
                                <i class="fa fa-plus-circle margin-right-5"></i> Create Project
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- PAGINATION -->
        <div class="d-flex justify-content-center margin-top-20">
            {{ $projects->links('pagination::bootstrap-4') }}
        </div>
    </div>
</x-layouts.app>
