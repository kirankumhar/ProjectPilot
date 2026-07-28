<x-layouts.app title="Edit Project - ProjectPilot">
    <div class="page-heading">
        <div class="page-heading__container">
            <h1 class="title">Edit Project</h1>
            <p class="caption">Update details for {{ $project->name }}</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projects</a></li>
                <li class="breadcrumb-item"><a href="{{ route('projects.show', $project) }}">{{ Str::limit($project->name, 20) }}</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-9">
                <div class="card margin-bottom-20">
                    <div class="card-body">
                        <form action="{{ route('projects.update', $project) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group margin-bottom-20">
                                <label for="name" class="font-weight-bold">Project Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $project->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group margin-bottom-20">
                                <label for="description" class="font-weight-bold">Project Description</label>
                                <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $project->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-row">
                                <div class="col-12 col-md-6 form-group margin-bottom-20">
                                    <label for="status" class="font-weight-bold">Status <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="pending" {{ old('status', $project->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in_progress" {{ old('status', $project->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="on_hold" {{ old('status', $project->status) == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                        <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6 form-group margin-bottom-20">
                                    <label for="priority" class="font-weight-bold">Priority <span class="text-danger">*</span></label>
                                    <select name="priority" id="priority" class="form-control @error('priority') is-invalid @enderror" required>
                                        <option value="low" {{ old('priority', $project->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('priority', $project->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('priority', $project->priority) == 'high' ? 'selected' : '' }}>High</option>
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-12 col-md-6 form-group margin-bottom-20">
                                    <label for="start_date" class="font-weight-bold">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', $project->start_date) }}">
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6 form-group margin-bottom-20">
                                    <label for="due_date" class="font-weight-bold">Due Date</label>
                                    <input type="date" name="due_date" id="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date', $project->due_date) }}">
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group margin-bottom-30">
                                <label class="font-weight-bold">Assign Team Members</label>
                                <div class="border rounded padding-15" style="max-height: 180px; overflow-y: auto;">
                                    <div class="row">
                                        @php
                                            $memberIds = $project->members->pluck('id')->toArray();
                                        @endphp
                                        @foreach($users as $user)
                                            <div class="col-12 col-sm-6 col-md-4 margin-bottom-10">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" name="members[]" value="{{ $user->id }}" id="user_{{ $user->id }}" class="custom-control-input" {{ in_array($user->id, old('members', $memberIds)) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="user_{{ $user->id }}">
                                                        <strong>{{ $user->name }}</strong>
                                                        <small class="text-muted d-block">{{ $user->role ?? 'Member' }}</small>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <a href="{{ route('projects.show', $project) }}" class="btn btn-light">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save margin-right-5"></i> Update Project
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
