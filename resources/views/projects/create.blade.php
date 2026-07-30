<x-layouts.app title="Create Project - ProjectPilot">
    <div class="page-heading">
        <div class="page-heading__container">
            <h1 class="title">Create New Project</h1>
            <p class="caption">Fill in details to set up a new project workspace</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projects</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </nav>
    </div>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-9">
                <div class="card margin-bottom-20">
                    <div class="card-body">
                        <form action="{{ route('projects.store') }}" method="POST">
                            @csrf

                            <div class="form-group margin-bottom-20">
                                <label for="name" class="font-weight-bold">Project Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Mobile App Redesign" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group margin-bottom-20">
                                <label for="description" class="font-weight-bold">Project Description</label>
                                <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror" placeholder="Describe the objectives, scope, and deliverables...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-row">
                                <div class="col-12 col-md-6 form-group margin-bottom-20">
                                    <label for="status" class="font-weight-bold">Status <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in_progress" {{ old('status', 'in_progress') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="on_hold" {{ old('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
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
                                <div class="col-12 col-md-6 form-group margin-bottom-20">
                                    <label for="start_date" class="font-weight-bold">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', now()->format('Y-m-d')) }}">
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6 form-group margin-bottom-20">
                                    <label for="due_date" class="font-weight-bold">Due Date</label>
                                    <input type="date" name="due_date" id="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date', now()->addDays(14)->format('Y-m-d')) }}">
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group margin-bottom-30">
                                <label class="font-weight-bold">Assign Team Members</label>
                                <div class="border rounded padding-15" style="max-height: 180px; overflow-y: auto;">
                                    <div class="row">
                                        @foreach($users as $user)
                                            <div class="col-12 col-sm-6 col-md-4 margin-bottom-10">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" name="members[]" value="{{ $user->id }}" id="user_{{ $user->id }}" class="custom-control-input" {{ (is_array(old('members')) && in_array($user->id, old('members'))) || $user->id === auth()->id() ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="user_{{ $user->id }}">
                                                        <strong>{{ $user->name }}</strong>
                                                        <small class="text-muted d-block">{{ $user->role_display }}</small>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <a href="{{ route('projects.index') }}" class="btn btn-light">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-check margin-right-5"></i> Create Project
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
