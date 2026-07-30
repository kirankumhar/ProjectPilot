<x-layouts.app title="Add Developer / Member - ProjectPilot">
    <div class="page-heading">
        <div class="page-heading__container">
            <h1 class="title">Add New Developer / Team Member</h1>
            <p class="caption">Create developer, frontend dev, backend dev, manager or admin accounts</p>
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
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-12 col-md-6 form-group margin-bottom-20">
                            <label for="name" class="font-weight-bold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Alexy Torenov" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6 form-group margin-bottom-20">
                            <label for="email" class="font-weight-bold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="e.g. alexy@projectpilot.com" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-6 form-group margin-bottom-20">
                            <label for="role" class="font-weight-bold">Role / Position <span class="text-danger">*</span></label>
                            <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" required>
                                <option value="">Select Role</option>
                                @foreach($roles as $key => $label)
                                    <option value="{{ $key }}" {{ old('role') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6 form-group margin-bottom-20">
                            <label class="font-weight-bold">Default Password</label>
                            <div class="alert alert-info py-2 px-3 m-0">
                                <i class="fa fa-info-circle margin-right-5"></i> Default password set automatically: <strong>password</strong>
                            </div>
                        </div>
                    </div>

                    <div class="margin-top-20">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-check-circle margin-right-5"></i> Create User / Developer
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-light margin-left-5">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
