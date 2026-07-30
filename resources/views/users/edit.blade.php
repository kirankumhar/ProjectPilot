<x-layouts.app title="Edit User - ProjectPilot">
    <div class="page-heading">
        <div class="page-heading__container">
            <h1 class="title">Edit User: {{ $user->name }}</h1>
            <p class="caption">Update user profile information and role permissions</p>
        </div>
        <div class="page-heading__container float-right">
            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left margin-right-5"></i> Back to Users List
            </a>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-12 col-md-6 form-group margin-bottom-20">
                            <label for="name" class="font-weight-bold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6 form-group margin-bottom-20">
                            <label for="email" class="font-weight-bold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 form-group margin-bottom-20">
                            <label for="role" class="font-weight-bold">Role / Position <span class="text-danger">*</span></label>
                            <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" required>
                                @foreach($roles as $key => $label)
                                    <option value="{{ $key }}" {{ old('role', $user->role) === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="margin-top-20">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save margin-right-5"></i> Update User
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-light margin-left-5">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
