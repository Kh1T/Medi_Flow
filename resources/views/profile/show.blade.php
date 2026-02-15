<x-admin title="Admin Profile">
    <div class="row">
        <!-- Profile Overview Card -->
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body text-center">
                    <div class="profile-pic mb-3">
                        @if($user->profile_photo)
                            <img src="{{ asset($user->profile_photo) }}" alt="Profile Photo" class="rounded-circle" width="120" height="120">
                        @else
                            <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                                <span class="text-white" style="font-size: 48px; font-weight: bold;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                    </div>
                    <h4 class="mb-2">{{ $user->name }}</h4>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    <span class="badge badge-opacity-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'doctor' ? 'success' : 'info') }} mb-3">
                        {{ ucfirst($user->role) }}
                    </span>
                    <div class="mt-3">
                        <p class="text-muted mb-1"><i class="mdi mdi-phone me-1"></i> {{ $user->phone ?? 'Not provided' }}</p>
                        <p class="text-muted"><i class="mdi mdi-calendar me-1"></i> Joined {{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Edit Form -->
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Profile Information</h4>
                    <p class="card-description">Update your account settings and personal information</p>
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-alert-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <form class="forms-sample" action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+1 (555) 123-4567">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="role">Role</label>
                            <input type="text" class="form-control" id="role" value="{{ ucfirst($user->role) }}" disabled>
                            <small class="form-text text-muted">Role cannot be changed from profile settings</small>
                        </div>
                        
                        <hr class="my-4">
                        
                        <h5 class="mb-3">Change Password</h5>
                        <p class="text-muted small mb-3">Leave password fields empty to keep your current password</p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">New Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Enter new password">
                                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation">Confirm Password</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm new password">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="mdi mdi-content-save me-1"></i>Save Changes
                            </button>
                            <a href="{{ route('home') }}" class="btn btn-light">
                                <i class="mdi mdi-arrow-left me-1"></i>Back to Dashboard
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Account Statistics (Optional) -->
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Account Information</h4>
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="mb-2">
                                <i class="mdi mdi-account-circle text-primary" style="font-size: 2rem;"></i>
                            </div>
                            <h6 class="text-muted">User ID</h6>
                            <h4>#{{ $user->id }}</h4>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="mb-2">
                                <i class="mdi mdi-shield-check text-success" style="font-size: 2rem;"></i>
                            </div>
                            <h6 class="text-muted">Account Status</h6>
                            <h4 class="text-success">Active</h4>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="mb-2">
                                <i class="mdi mdi-calendar-clock text-info" style="font-size: 2rem;"></i>
                            </div>
                            <h6 class="text-muted">Last Updated</h6>
                            <h4>{{ $user->updated_at->format('M d') }}</h4>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="mb-2">
                                <i class="mdi mdi-email-check text-warning" style="font-size: 2rem;"></i>
                            </div>
                            <h6 class="text-muted">Email Status</h6>
                            <h4 class="text-{{ $user->email_verified_at ? 'success' : 'warning' }}">
                                {{ $user->email_verified_at ? 'Verified' : 'Unverified' }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin>
