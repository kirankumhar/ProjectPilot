<x-layouts.app title="Create Task - ProjectPilot">
    <div class="page-heading">
        <div class="page-heading__container">
            <h1 class="title">Create Task</h1>
            <p class="caption">Add a new task and assign it to a team member</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tasks.index') }}">Tasks</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </nav>
    </div>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="card margin-bottom-20">
                    <div class="card-body">
                        <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group margin-bottom-20">
                                <label for="title" class="font-weight-bold">Task Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" placeholder="e.g. Implement user login validation" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- TASK TYPE SELECTION -->
                            <div class="form-group margin-bottom-20">
                                <label for="type" class="font-weight-bold">Task Category / Type <span class="text-danger">*</span></label>
                                <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                                    <option value="feature" {{ old('type', 'feature') == 'feature' ? 'selected' : '' }}>✨ Feature (New Functionality)</option>
                                    <option value="bug_fix" {{ old('type') == 'bug_fix' ? 'selected' : '' }}>🐛 Bug Fix (Defect / Issue)</option>
                                    <option value="maintenance" {{ old('type') == 'maintenance' ? 'selected' : '' }}>🔧 Maintenance (Refactoring, Updates, Server)</option>
                                    <option value="support" {{ old('type') == 'support' ? 'selected' : '' }}>🎧 Support Ticket (User Help / Inquiries)</option>
                                    <option value="cr" {{ old('type') == 'cr' ? 'selected' : '' }}>📝 Change Request (CR / Scope Modification)</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group margin-bottom-20">
                                <label for="description" class="font-weight-bold">Description</label>
                                <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror" placeholder="Details about requirements or steps...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-row">
                                <div class="col-12 col-md-6 form-group margin-bottom-20">
                                    <label for="project_id" class="font-weight-bold">Project <span class="text-danger">*</span></label>
                                    <select name="project_id" id="project_id" class="form-control @error('project_id') is-invalid @enderror" required>
                                        <option value="">Select Project</option>
                                        @foreach($projects as $p)
                                            <option value="{{ $p->id }}" {{ old('project_id', $selectedProjectId) == $p->id ? 'selected' : '' }}>
                                                {{ $p->name }} ({{ $p->type_display }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('project_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6 form-group margin-bottom-20">
                                    <label for="assigned_to" class="font-weight-bold">Assignee <span class="text-danger">*</span></label>
                                    <select name="assigned_to" id="assigned_to" class="form-control @error('assigned_to') is-invalid @enderror" required>
                                        <option value="">Select User</option>
                                        @foreach($users as $u)
                                            <option value="{{ $u->id }}" {{ old('assigned_to', auth()->id()) == $u->id ? 'selected' : '' }}>
                                                {{ $u->name }} ({{ $u->role_display }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('assigned_to')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-12 col-md-6 form-group margin-bottom-20">
                                    <label for="status" class="font-weight-bold">Status <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6 form-group margin-bottom-20">
                                    <label for="priority" class="font-weight-bold">Priority <span class="text-danger">*</span></label>
                                    <select name="priority" id="priority" class="form-control @error('priority') is-invalid @enderror" required>
                                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-12 col-md-4 form-group margin-bottom-20">
                                    <label for="start_date" class="font-weight-bold">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', now()->format('Y-m-d')) }}">
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-4 form-group margin-bottom-20">
                                    <label for="due_date" class="font-weight-bold">Due Date</label>
                                    <input type="date" name="due_date" id="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date', now()->addDays(7)->format('Y-m-d')) }}">
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-4 form-group margin-bottom-20">
                                    <label for="estimated_hours" class="font-weight-bold">Estimated Hours</label>
                                    <div class="input-group">
                                        <input type="number" step="0.25" min="0" max="9999" name="estimated_hours" id="estimated_hours" class="form-control @error('estimated_hours') is-invalid @enderror" placeholder="e.g. 8.0" value="{{ old('estimated_hours') }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text">hrs</span>
                                        </div>
                                    </div>
                                    @error('estimated_hours')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group margin-bottom-20">
                                <label for="attachment" class="font-weight-bold">Attach File (Optional)</label>
                                <input type="file" name="attachment" id="attachment" class="form-control-file @error('attachment') is-invalid @enderror">
                                <small class="form-text text-muted">Allowed files: Images, PDF, DOCX, ZIP, TXT (Max size: 10MB)</small>
                                @error('attachment')
                                    <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <a href="{{ route('tasks.index') }}" class="btn btn-light">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-check margin-right-5"></i> Create Task
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
