@extends(auth()->user()->hasRole('admin') ? 'layouts.admin' : 'layouts.professor')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Register New Student</h1>
        <div>
            <a href="{{ route('students.import.form') }}" class="btn btn-info mr-2">
                <i class="fas fa-file-csv"></i> Bulk Import CSV
            </a>
            <a href="{{ route('students.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-user-plus"></i> Student Information
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('students.store') }}" method="POST">
                @csrf

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> <strong>Note:</strong> 
                    Please enter the student's complete name in separate fields. Middle name is optional.
                </div>

                <!-- Name Fields -->
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
                                   placeholder="e.g., Juan">
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
                                   placeholder="e.g., Santos">
                            @error('middle_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Leave blank if not applicable</small>
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
                                   placeholder="e.g., Dela Cruz">
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
                                   placeholder="e.g., juan.delacruz@philsca.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">This will be used for login</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="year_level" class="form-label">
                                Year Level <span class="text-danger">*</span>
                            </label>
                            <select class="form-control @error('year_level') is-invalid @enderror" 
                                    id="year_level" 
                                    name="year_level" 
                                    required>
                                <option value="">-- Select Year Level --</option>
                                <option value="1st Year" {{ old('year_level') == '1st Year' ? 'selected' : '' }}>1st Year</option>
                                <option value="2nd Year" {{ old('year_level') == '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                                <option value="3rd Year" {{ old('year_level') == '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                                <option value="4th Year" {{ old('year_level') == '4th Year' ? 'selected' : '' }}>4th Year</option>
                            </select>
                            @error('year_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="my-4">
                <h6 class="text-muted border-bottom pb-2 mb-3">Set Initial Password</h6>

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
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-save"></i> Register Student
                    </button>
                    <a href="{{ route('students.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4 border-info">
        <div class="card-body">
            <h6 class="text-info mb-3">
                <i class="fas fa-lightbulb"></i> Registration Tips
            </h6>
            <ul class="mb-0">
                <li>Enter names exactly as they appear in official documents</li>
                <li>Middle name is optional - leave blank if the student has no middle name</li>
                <li>Email addresses must be unique across all users</li>
                <li>After registration, you can enroll students in sections using CSV enrollment</li>
            </ul>
        </div>
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
