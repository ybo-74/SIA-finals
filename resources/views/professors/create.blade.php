@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Register New Professor</h1>
        <a href="{{ route('professors.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-chalkboard-teacher"></i> Professor Information
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('professors.store') }}" method="POST">
                @csrf

                <h6 class="text-muted border-bottom pb-2 mb-3">Personal Information</h6>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="first_name" class="form-label">
                                First Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('first_name') is-invalid @enderror" 
                                   id="first_name" 
                                   name="first_name" 
                                   value="{{ old('first_name') }}" 
                                   required
                                   autofocus
                                   placeholder="e.g., Aron">
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="middle_name" class="form-label">
                                Middle Name <small class="text-muted">(Optional)</small>
                            </label>
                            <input type="text" 
                                   class="form-control @error('middle_name') is-invalid @enderror" 
                                   id="middle_name" 
                                   name="middle_name" 
                                   value="{{ old('middle_name') }}"
                                   placeholder="e.g., J.">
                            @error('middle_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="last_name" class="form-label">
                                Last Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('last_name') is-invalid @enderror" 
                                   id="last_name" 
                                   name="last_name" 
                                   value="{{ old('last_name') }}" 
                                   required
                                   placeholder="e.g., Alojado">
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Preview Full Name -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-light">
                            <small class="text-muted">
                                <strong>Full Name Preview:</strong> 
                                <span id="namePreview" class="text-dark">Enter name fields to see preview</span>
                            </small>
                        </div>
                    </div>
                </div>

                <hr class="my-4">
                <h6 class="text-muted border-bottom pb-2 mb-3">Account Information</h6>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                Email Address <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required
                                   placeholder="e.g., professor@philsca.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="my-4">
                <h6 class="text-muted border-bottom pb-2 mb-3">Set Password</h6>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                Password <span class="text-danger">*</span>
                            </label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Minimum 8 characters</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">
                                Confirm Password <span class="text-danger">*</span>
                            </label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Register Professor
                    </button>
                    <a href="{{ route('professors.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> <strong>Note:</strong> 
        After registration, you can assign subjects to the professor in the Subjects section.
    </div>
</div>

<script>
// Real-time name preview
function updateNamePreview() {
    const firstName = document.getElementById('first_name').value.trim();
    const middleName = document.getElementById('middle_name').value.trim();
    const lastName = document.getElementById('last_name').value.trim();
    
    let fullName = '';
    if (firstName || lastName) {
        const parts = [firstName, middleName, lastName].filter(part => part !== '');
        fullName = parts.join(' ');
    }
    
    const preview = document.getElementById('namePreview');
    if (fullName) {
        preview.textContent = fullName;
        preview.className = 'text-dark font-weight-bold';
    } else {
        preview.textContent = 'Enter name fields to see preview';
        preview.className = 'text-muted';
    }
}

// Attach event listeners
document.addEventListener('DOMContentLoaded', function() {
    ['first_name', 'middle_name', 'last_name'].forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', updateNamePreview);
        }
    });
    
    // Initial preview
    updateNamePreview();
});
</script>
@endsection
