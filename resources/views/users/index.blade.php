<x-layouts.app title="Team & Users - ProjectPilot">
    <div class="page-heading">
        <div class="page-heading__container">
            <h1 class="title">Team Members & Developers</h1>
            <p class="caption">Manage system users, assigned roles, and developer accounts</p>
        </div>
        <div class="page-heading__container float-right">
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fa fa-plus-circle margin-right-5"></i> Add Developer / Member
            </a>
        </div>
    </div>

    <div class="container-fluid">
        <!-- FILTER BAR -->
        <div class="card margin-bottom-20">
            <div class="card-body">
                <form action="{{ route('users.index') }}" method="GET" class="form-row align-items-center">
                    <div class="col-12 col-md-5 margin-bottom-10">
                        <input type="text" name="search" class="form-control" placeholder="Search member by name or email..." value="{{ request('search') }}">
                    </div>
                    <div class="col-12 col-md-4 margin-bottom-10">
                        <select name="role" class="form-control">
                            <option value="">All Roles</option>
                            @foreach(\App\Models\User::ROLES as $key => $label)
                                <option value="{{ $key }}" {{ request('role') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-3 margin-bottom-10">
                        <button type="submit" class="btn btn-secondary btn-block">
                            <i class="fa fa-filter margin-right-5"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- USERS TABLE -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Projects</th>
                                <th>Assigned Tasks</th>
                                <th>Joined Date</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                    <td>
                                        <strong>{{ $user->name }}</strong>
                                        @if(auth()->id() === $user->id)
                                            <span class="badge badge-info ml-1">You</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->role === 'admin')
                                            <span class="badge badge-danger">Admin</span>
                                        @elseif($user->role === 'manager')
                                            <span class="badge badge-warning">Manager</span>
                                        @elseif($user->role === 'backend_dev')
                                            <span class="badge badge-primary">Backend Developer</span>
                                        @elseif($user->role === 'frontend_dev')
                                            <span class="badge badge-info">Frontend Developer</span>
                                        @else
                                            <span class="badge badge-secondary">{{ $user->role_display }}</span>
                                        @endif
                                    </td>
                                    <td><span class="badge badge-light">{{ $user->projects_count }}</span></td>
                                    <td><span class="badge badge-light">{{ $user->assigned_tasks_count }}</span></td>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                    <td class="text-right">
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-secondary margin-right-5" title="Edit User">
                                            <i class="fa fa-pencil"></i> Edit
                                        </a>
                                        @if(auth()->id() !== $user->id)
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete User">
                                                    <i class="fa fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        No team members found. <a href="{{ route('users.create') }}">Click here to add one.</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($users->hasPages())
                <div class="card-footer">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
