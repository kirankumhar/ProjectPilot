<x-layouts.app title="Developer Profile - ProjectPilot">
    <!-- PAGE HEADING -->
    <div class="page-heading">
        <div class="page-heading__container">
            <h1 class="title">Developer Profile</h1>
            <p class="caption">Manage your developer profile details, update profile picture, and change password</p>
        </div>
    </div>
    <!-- //END PAGE HEADING -->

    <div class="container-fluid">
        <!-- DEVELOPER HERO CARD -->
        <div class="card margin-bottom-20 shadow-sm border-0">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-12 col-md-auto text-center text-md-left mb-3 mb-md-0">
                        <div class="position-relative d-inline-block">
                            <img id="hero-avatar-preview" 
                                 src="{{ $user->avatar_url }}" 
                                 alt="{{ $user->name }}" 
                                 class="rounded-circle img-thumbnail shadow-sm" 
                                 style="width: 110px; height: 110px; object-fit: cover; border: 4px solid #fff;">
                            <span class="position-absolute bottom-0 right-0 badge badge-success rounded-circle p-2" 
                                  title="Active Account" 
                                  style="width: 22px; height: 22px; right: 5px; bottom: 5px; border: 2px solid #fff;">
                            </span>
                        </div>
                    </div>
                    <div class="col-12 col-md text-center text-md-left">
                        <h2 class="h3 font-weight-bold text-dark mb-1">{{ $user->name }}</h2>
                        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-2 mb-2">
                            <span class="badge badge-primary px-3 py-2 text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.5px;">
                                <i class="fa fa-code margin-right-5"></i>{{ $user->role_display }}
                            </span>
                            <span class="text-muted ml-2">
                                <i class="fa fa-envelope margin-right-5 text-primary"></i>{{ $user->email }}
                            </span>
                            <span class="text-muted ml-3">
                                <i class="fa fa-calendar margin-right-5 text-info"></i>Joined {{ $user->created_at ? $user->created_at->format('M Y') : 'N/A' }}
                            </span>
                        </div>
                        <p class="text-secondary small mb-0">ProjectPilot Registered Developer • ID #{{ sprintf('%04d', $user->id) }}</p>
                    </div>
                    <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                        <div class="row text-center">
                            <div class="col-4 col-sm-4 px-2">
                                <div class="p-3 bg-light rounded shadow-xs">
                                    <div class="h4 font-weight-bold text-primary mb-0">{{ $stats['assigned_tasks'] ?? 0 }}</div>
                                    <div class="small text-muted text-uppercase" style="font-size: 0.7rem;">Tasks</div>
                                </div>
                            </div>
                            <div class="col-4 col-sm-4 px-2">
                                <div class="p-3 bg-light rounded shadow-xs">
                                    <div class="h4 font-weight-bold text-success mb-0">{{ $stats['completed_tasks'] ?? 0 }}</div>
                                    <div class="small text-muted text-uppercase" style="font-size: 0.7rem;">Done</div>
                                </div>
                            </div>
                            <div class="col-4 col-sm-4 px-2">
                                <div class="p-3 bg-light rounded shadow-xs">
                                    <div class="h4 font-weight-bold text-info mb-0">{{ $stats['projects'] ?? 0 }}</div>
                                    <div class="small text-muted text-uppercase" style="font-size: 0.7rem;">Projects</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- LEFT COLUMN: PROFILE & PHOTO EDIT -->
            <div class="col-12 col-lg-7 margin-bottom-20">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center margin-right-15" style="width: 40px; height: 40px;">
                                <i class="fa fa-user-circle fa-lg"></i>
                            </div>
                            <div>
                                <h4 class="card-title mb-0">Profile Information & Photo</h4>
                                <small class="text-muted">Update your developer details and profile picture</small>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('patch')

                            <!-- AVATAR UPLOAD SECTION -->
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark mb-2">Profile Picture (Avatar)</label>
                                <div class="d-flex align-items-center">
                                    <div class="margin-right-20">
                                        <img id="avatar-preview-img" 
                                             src="{{ $user->avatar_url }}" 
                                             alt="Avatar Preview" 
                                             class="rounded-circle img-thumbnail shadow-xs" 
                                             style="width: 85px; height: 85px; object-fit: cover;">
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="custom-file mb-2">
                                            <input type="file" 
                                                   class="custom-file-input @error('avatar') is-invalid @enderror" 
                                                   id="avatar" 
                                                   name="avatar" 
                                                   accept="image/*" 
                                                   onchange="previewSelectedAvatar(event)">
                                            <label class="custom-file-label" for="avatar" id="avatar-label">Choose new picture...</label>
                                        </div>
                                        <small class="text-muted d-block mb-2">
                                            <i class="fa fa-info-circle text-info"></i> Allowed formats: JPG, PNG, WEBP, GIF (Max size: 2MB)
                                        </small>

                                        @if($user->avatar)
                                            <div class="custom-control custom-checkbox mt-1">
                                                <input type="checkbox" class="custom-control-input" id="remove_avatar" name="remove_avatar" value="1" onchange="toggleRemoveAvatarNotice(this)">
                                                <label class="custom-control-label text-danger font-weight-bold" for="remove_avatar">
                                                    <i class="fa fa-trash margin-right-5"></i> Remove custom profile picture
                                                </label>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @error('avatar')
                                    <div class="text-danger small mt-2"><i class="fa fa-exclamation-triangle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="my-4">

                            <!-- NAME FIELD -->
                            <div class="form-group mb-3">
                                <label for="name" class="font-weight-bold text-dark">
                                    Full Name <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-user"></i></span>
                                    </div>
                                    <input type="text" 
                                           id="name" 
                                           name="name" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $user->name) }}" 
                                           required 
                                           placeholder="Enter developer full name">
                                </div>
                                @error('name')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- EMAIL FIELD -->
                            <div class="form-group mb-3">
                                <label for="email" class="font-weight-bold text-dark">
                                    Email Address <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                                    </div>
                                    <input type="email" 
                                           id="email" 
                                           name="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           value="{{ old('email', $user->email) }}" 
                                           required 
                                           placeholder="developer@projectpilot.com">
                                </div>
                                @error('email')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- ROLE / SPECIALIZATION FIELD -->
                            <div class="form-group mb-4">
                                <label for="role" class="font-weight-bold text-dark">
                                    Developer Role / Position
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-briefcase"></i></span>
                                    </div>
                                    <select id="role" name="role" class="form-control @error('role') is-invalid @enderror">
                                        <option value="developer" {{ old('role', $user->role) == 'developer' ? 'selected' : '' }}>Developer</option>
                                        <option value="backend_dev" {{ old('role', $user->role) == 'backend_dev' ? 'selected' : '' }}>Backend Developer</option>
                                        <option value="frontend_dev" {{ old('role', $user->role) == 'frontend_dev' ? 'selected' : '' }}>Frontend Developer</option>
                                        <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>Manager</option>
                                        @if($user->isAdmin())
                                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                        @endif
                                    </select>
                                </div>
                                @error('role')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="d-flex align-items-center justify-content-between pt-2">
                                <button type="submit" class="btn btn-primary px-4 py-2">
                                    <i class="fa fa-save margin-right-5"></i> Save Profile Details
                                </button>
                                
                                @if (session('status') === 'profile-updated')
                                    <span class="text-success font-weight-bold animated fadeIn">
                                        <i class="fa fa-check-circle"></i> Profile updated successfully!
                                    </span>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: PASSWORD & SECURITY -->
            <div class="col-12 col-lg-5 margin-bottom-20">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center margin-right-15" style="width: 40px; height: 40px;">
                                <i class="fa fa-lock fa-lg"></i>
                            </div>
                            <div>
                                <h4 class="card-title mb-0">Change Password</h4>
                                <small class="text-muted">Ensure your account is secured with a strong password</small>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            @method('put')

                            <!-- CURRENT PASSWORD -->
                            <div class="form-group mb-3">
                                <label for="update_password_current_password" class="font-weight-bold text-dark">
                                    Current Password
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-key"></i></span>
                                    </div>
                                    <input type="password" 
                                           id="update_password_current_password" 
                                           name="current_password" 
                                           class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
                                           autocomplete="current-password" 
                                           placeholder="••••••••">
                                </div>
                                @if($errors->updatePassword->has('current_password'))
                                    <div class="text-danger small mt-1">
                                        <i class="fa fa-exclamation-circle"></i> {{ $errors->updatePassword->first('current_password') }}
                                    </div>
                                @endif
                            </div>

                            <!-- NEW PASSWORD -->
                            <div class="form-group mb-3">
                                <label for="update_password_password" class="font-weight-bold text-dark">
                                    New Password
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-lock"></i></span>
                                    </div>
                                    <input type="password" 
                                           id="update_password_password" 
                                           name="password" 
                                           class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
                                           autocomplete="new-password" 
                                           placeholder="••••••••">
                                </div>
                                @if($errors->updatePassword->has('password'))
                                    <div class="text-danger small mt-1">
                                        <i class="fa fa-exclamation-circle"></i> {{ $errors->updatePassword->first('password') }}
                                    </div>
                                @endif
                            </div>

                            <!-- CONFIRM NEW PASSWORD -->
                            <div class="form-group mb-4">
                                <label for="update_password_password_confirmation" class="font-weight-bold text-dark">
                                    Confirm New Password
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-check-circle"></i></span>
                                    </div>
                                    <input type="password" 
                                           id="update_password_password_confirmation" 
                                           name="password_confirmation" 
                                           class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" 
                                           autocomplete="new-password" 
                                           placeholder="••••••••">
                                </div>
                                @if($errors->updatePassword->has('password_confirmation'))
                                    <div class="text-danger small mt-1">
                                        <i class="fa fa-exclamation-circle"></i> {{ $errors->updatePassword->first('password_confirmation') }}
                                    </div>
                                @endif
                            </div>

                            <!-- PASSWORD HINT BOX -->
                            <div class="alert alert-light border p-3 rounded mb-4">
                                <small class="text-muted d-block">
                                    <strong class="text-dark"><i class="fa fa-shield text-success"></i> Security Tip:</strong><br>
                                    Use at least 8 characters with a combination of letters, numbers, and special characters.
                                </small>
                            </div>

                            <div class="d-flex align-items-center justify-content-between">
                                <button type="submit" class="btn btn-success px-4 py-2">
                                    <i class="fa fa-key margin-right-5"></i> Update Password
                                </button>
                                
                                @if (session('status') === 'password-updated')
                                    <span class="text-success font-weight-bold">
                                        <i class="fa fa-check-circle"></i> Password updated!
                                    </span>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                <!-- DANGER ZONE: DELETE ACCOUNT -->
                <div class="card shadow-sm border-danger">
                    <div class="card-header bg-white border-bottom-0 pb-0">
                        <h5 class="text-danger mb-0"><i class="fa fa-exclamation-triangle"></i> Account Management</h5>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">Once your account is deleted, all resources and data will be permanently removed.</p>
                        <button type="button" class="btn btn-outline-danger btn-sm" data-toggle="modal" data-target="#deleteAccountModal">
                            <i class="fa fa-trash"></i> Delete Account
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DELETE ACCOUNT MODAL -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" role="dialog" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')
                    
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteAccountModalLabel">Confirm Account Deletion</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="text-dark font-weight-bold mb-2">Are you sure you want to delete your developer account?</p>
                        <p class="text-muted small">Please enter your password to confirm you would like to permanently delete your account.</p>
                        
                        <div class="form-group mt-3">
                            <input type="password" name="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror" placeholder="Enter your password" required>
                            @if($errors->userDeletion->has('password'))
                                <div class="text-danger small mt-1">{{ $errors->userDeletion->first('password') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- AVATAR PREVIEW JAVASCRIPT -->
    <script>
        function previewSelectedAvatar(event) {
            const input = event.target;
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('avatar-preview-img');
                    const heroPreview = document.getElementById('hero-avatar-preview');
                    if (preview) preview.src = e.target.result;
                    if (heroPreview) heroPreview.src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);

                const label = document.getElementById('avatar-label');
                if (label) label.textContent = input.files[0].name;

                const removeCheckbox = document.getElementById('remove_avatar');
                if (removeCheckbox) removeCheckbox.checked = false;
            }
        }

        function toggleRemoveAvatarNotice(checkbox) {
            if (checkbox.checked) {
                const avatarInput = document.getElementById('avatar');
                if (avatarInput) avatarInput.value = '';
                const label = document.getElementById('avatar-label');
                if (label) label.textContent = 'Choose new picture...';
            }
        }
    </script>
</x-layouts.app>
