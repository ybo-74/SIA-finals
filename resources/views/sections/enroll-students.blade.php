@extends('layouts.professor')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-gray-800">Enroll Students via CSV</h1>
            <p class="text-muted mb-0">
                Section: <strong>{{ $section->name }}</strong> | 
                Subject: <strong>{{ $section->subject->code }} - {{ $section->subject->name }}</strong>
            </p>
        </div>
        <a href="{{ route('sections.show', $section) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Section
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    @if(session('import_errors') && count(session('import_errors')) > 0)
        <div class="alert alert-warning alert-dismissible fade show">
            <strong>Some rows had issues:</strong>
            <ul class="mb-0 mt-2">
                @foreach(session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <!-- Instructions Card -->
    <div class="card shadow mb-4 border-info">
        <div class="card-header bg-info text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-info-circle"></i> CSV Format Instructions
            </h6>
        </div>
        <div class="card-body">
            <h6 class="text-primary">CSV File Requirements:</h6>
            <ul>
                <li>CSV must have a header row with column: <code>email</code></li>
                <li>Each row should contain a student's email address</li>
                <li>The email must belong to an existing student account</li>
                <li>Students already enrolled will be skipped</li>
            </ul>

            <h6 class="text-success mt-3">Example CSV Format:</h6>
            <div class="bg-light p-3 border rounded">
                <code>
                    email<br>
                    student1@philsca.com<br>
                    student2@philsca.com<br>
                    student3@philsca.com
                </code>
            </div>

            <div class="mt-3">
                <a href="#" class="btn btn-sm btn-outline-success" onclick="downloadSampleCSV()">
                    <i class="fas fa-download"></i> Download Sample CSV
                </a>
            </div>
        </div>
    </div>

    <!-- Upload Form Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-upload"></i> Upload CSV File
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('sections.enroll.csv', $section) }}" 
                  method="POST" 
                  enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="csv_file" class="form-label">
                        Select CSV File <span class="text-danger">*</span>
                    </label>
                    <input type="file" 
                           class="form-control @error('csv_file') is-invalid @enderror" 
                           id="csv_file" 
                           name="csv_file" 
                           accept=".csv,.txt" 
                           required>
                    @error('csv_file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        Accepted formats: .csv, .txt (CSV format)
                    </small>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload and Enroll Students
                    </button>
                    <a href="{{ route('sections.show', $section) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Current Students Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-success text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-users"></i> Currently Enrolled Students ({{ $section->students->count() }})
            </h6>
        </div>
        <div class="card-body">
            @if($section->students->isEmpty())
                <p class="text-muted mb-0">No students enrolled yet.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Enrolled Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($section->students as $index => $student)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->email }}</td>
                                    <td>{{ $student->pivot->created_at->format('M d, Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function downloadSampleCSV() {
    // Create sample CSV content
    const csvContent = "email\nstudent1@philsca.com\nstudent2@philsca.com\nstudent3@philsca.com";
    
    // Create blob and download
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'sample_student_enrollment.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}
</script>
@endsection
