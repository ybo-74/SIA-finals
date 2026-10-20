@extends(auth()->user()->hasRole('admin') ? 'layouts.admin' : (auth()->user()->hasRole('professor') ? 'layouts.professor' : 'layouts.student'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Profile Settings</h1>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    @if(session('status') === 'profile-updated')
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> Profile updated successfully!
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    @if(session('status') === 'password-updated')
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> Password updated successfully!
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="row">
        <!-- Profile Information Card -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 
                    @if(auth()->user()->hasRole('admin')) bg-primary 
                    @elseif(auth()->user()->hasRole('professor')) bg-secondary 
                    @else bg-info 
                    @endif text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-user"></i> Profile Information
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $user->name) }}" 
                                   required 
                                   readonly
                                   style="background-color: #f8f9fc;">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Name cannot be changed. Contact admin if needed.
                            </small>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $user->email) }}" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn 
                            @if(auth()->user()->hasRole('admin')) btn-primary 
                            @elseif(auth()->user()->hasRole('professor')) btn-secondary 
                            @else btn-info 
                            @endif">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Change Password Card -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 
                    @if(auth()->user()->hasRole('admin')) bg-primary 
                    @elseif(auth()->user()->hasRole('professor')) bg-secondary 
                    @else bg-info 
                    @endif text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-lock"></i> Change Password
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" 
                                   class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
                                   id="current_password" 
                                   name="current_password" 
                                   required>
                            @error('current_password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="password" 
                                   class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required>
                            @error('password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Minimum 8 characters</small>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirm New Password</label>
                            <input type="password" 
                                   class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required>
                            @error('password_confirmation', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn 
                            @if(auth()->user()->hasRole('admin')) btn-primary 
                            @elseif(auth()->user()->hasRole('professor')) btn-secondary 
                            @else btn-info 
                            @endif">
                            <i class="fas fa-key"></i> Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Information Card -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4 border-left-info">
                <div class="card-body">
                    <h6 class="font-weight-bold text-info mb-3">
                        <i class="fas fa-info-circle"></i> Account Information
                    </h6>
                    <div class="row">
                        <div class="col-md-4">
                            <p class="mb-2">
                                <strong>Role:</strong> 
                                <span class="badge 
                                    @if(auth()->user()->hasRole('admin')) badge-primary 
                                    @elseif(auth()->user()->hasRole('professor')) badge-secondary 
                                    @else badge-info 
                                    @endif">
                                    {{ ucfirst(auth()->user()->getRoleNames()->first()) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-2">
                                <strong>Account Created:</strong> 
                                {{ auth()->user()->created_at->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-2">
                                <strong>Last Updated:</strong> 
                                {{ auth()->user()->updated_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
