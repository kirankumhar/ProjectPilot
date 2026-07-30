<x-layouts.app title="Edit Task - ProjectPilot">
    <div class="page-heading">
        <div class="page-heading__container">
            <h1 class="title">Edit Task</h1>
            <p class="caption">Update task details and progress</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tasks.index') }}">Tasks</a></li>
                <li class="breadcrumb-item active">Edit Task</li>
            </ol>
        </nav>
    </div>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="card margin-bottom-20">
                    <div class="card-body">
                        <form action="{{ route('tasks.update', $task) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="form-group margin-bottom-20">
                                <label for="title" class="font-weight-bold">Task Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $task->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- TASK TYPE SELECTION -->
                            <div class="form-group margin-bottom-20">
                                <label for="type" class="font-weight-bold">Task Category / Type <span class="text-danger">*</span></label>
                                <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                                    <option value="feature" {{ old('type', $task->type) == 'feature' ? 'selected' : '' }}>✨ Feature (New Functionality)</option>
                                    <option value="bug_fix" {{ old('type', $task->type) == 'bug_fix' ? 'selected' : '' }}>🐛 Bug Fix (Defect / Issue)</option>
                                    <option value="maintenance" {{ old('type', $task->type) == 'maintenance' ? 'selected' : '' }}>🔧 Maintenance (Refactoring, Updates, Server)</option>
                                    <option value="support" {{ old('type', $task->type) == 'support' ? 'selected' : '' }}>🎧 Support Ticket (User Help / Inquiries)</option>
                                    <option value="cr" {{ old('type', $task->type) == 'cr' ? 'selected' : '' }}>📝 Change Request (CR / Scope Modification)</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group margin-bottom-20">
                                <label for="description" class="font-weight-bold">Description</label>
                                <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $task->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-row">
                                <div class="col-12 col-md-6 form-group margin-bottom-20">
                                    <label for="project_id" class="font-weight-bold">Project <span class="text-danger">*</span></label>
                                    <select name="project_id" id="project_id" class="form-control @error('project_id') is-invalid @enderror" required>
                                        @foreach($projects as $p)
                                            <option value="{{ $p->id }}" {{ old('project_id', $task->project_id) == $p->id ? 'selected' : '' }}>
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
                                        @foreach($users as $u)
                                            <option value="{{ $u->id }}" {{ old('assigned_to', $task->assigned_to) == $u->id ? 'selected' : '' }}>
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
                                        <option value="pending" {{ old('status', $task->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6 form-group margin-bottom-20">
                                    <label for="priority" class="font-weight-bold">Priority <span class="text-danger">*</span></label>
                                    <select name="priority" id="priority" class="form-control @error('priority') is-invalid @enderror" required>
                                        <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>High</option>
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-12 col-md-6 form-group margin-bottom-20">
                                    <label for="start_date" class="font-weight-bold">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', $task->start_date) }}">
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6 form-group margin-bottom-20">
                                    <label for="due_date" class="font-weight-bold">Due Date</label>
                                    <input type="date" name="due_date" id="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date', $task->due_date) }}">
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group margin-bottom-20">
                                <label for="attachment" class="font-weight-bold">Attachment File</label>
                                @if($task->attachment)
                                    <div class="mb-2 p-2 bg-light border rounded d-flex align-items-center justify-content-between">
                                        <div>
                                            <i class="fa fa-paperclip text-primary margin-right-5"></i>
                                            <strong>Current File:</strong> {{ $task->attachment_name }}
                                        </div>
                                        <a href="{{ $task->attachment_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fa fa-download margin-right-5"></i> View / Download
                                        </a>
                                    </div>
                                @endif
                                <input type="file" name="attachment" id="attachment" class="form-control-file @error('attachment') is-invalid @enderror">
                                <small class="form-text text-muted">Upload new file to replace existing attachment (Max: 10MB)</small>
                                @error('attachment')
                                    <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <a href="{{ route('tasks.index') }}" class="btn btn-light">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save margin-right-5"></i> Update Task
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
