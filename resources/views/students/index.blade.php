@extends(auth()->user()->hasRole('admin') ? 'layouts.admin' : 'layouts.professor')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 text-gray-800">
        Students 
        @if(auth()->user()->hasRole('admin'))
            <span class="badge badge-primary">Admin View</span>
        @else
            <span class="badge badge-secondary">Professor View</span>
        @endif
    </h1>
    
    @if(auth()->user()->hasRole('professor'))
        <div>
            <a href="{{ route('students.create') }}" class="btn btn-secondary mr-2">
                <i class="fas fa-user-plus"></i> Register Student
            </a>
            <a href="{{ route('students.import.form') }}" class="btn btn-info">
                <i class="fas fa-file-csv"></i> Import CSV
            </a>
        </div>
    @endif
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
@endif

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Students
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($totalStudents) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Enrolled
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($enrolledStudents) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Not Enrolled
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($notEnrolledStudents) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($students->isEmpty() && !request('search'))
    <div class="alert alert-warning">
        No students registered yet. Students will appear here once professors enroll them in sections.
    </div>
@else
    <div class="card shadow">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-user-graduate"></i> Student List (Alphabetical by Surname)
                <small class="ml-2">({{ $students->total() }} {{ Str::plural('student', $students->total()) }})</small>
            </h6>
        </div>
        <div class="card-body">
            <!-- Search and Filter Form -->
            <form method="GET" action="{{ route('students.index') }}" class="mb-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="Search by name or email..." 
                                   value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i> Search
                                </button>
                                @if(request('search'))
                                    <a href="{{ route('students.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Clear
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="sort_by" class="form-control" onchange="this.form.submit()">
                            <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>
                                Sort by Name (Surname)
                            </option>
                            <option value="email" {{ request('sort_by') == 'email' ? 'selected' : '' }}>
                                Sort by Email
                            </option>
                            <option value="year_level" {{ request('sort_by') == 'year_level' ? 'selected' : '' }}>
                                Sort by Year Level
                            </option>
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>
                                Sort by Registration Date
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="per_page" class="form-control" onchange="this.form.submit()">
                            <option value="10" {{ request('per_page', 25) == 10 ? 'selected' : '' }}>
                                Show 10 per page
                            </option>
                            <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>
                                Show 25 per page
                            </option>
                            <option value="50" {{ request('per_page', 25) == 50 ? 'selected' : '' }}>
                                Show 50 per page
                            </option>
                            <option value="100" {{ request('per_page', 25) == 100 ? 'selected' : '' }}>
                                Show 100 per page
                            </option>
                        </select>
                    </div>
                </div>
            </form>

            @if($students->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No students found matching your search criteria.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 30%">
                                    Name (Surname, First Middle)
                                    @if(request('sort_by') == 'name')
                                        <i class="fas fa-sort-{{ request('sort_direction', 'asc') == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                <th style="width: 25%">Email</th>
                                <th style="width: 15%" class="text-center">Year Level</th>
                                <th style="width: 10%" class="text-center">Sections</th>
                                <th style="width: 15%">Registered</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $index => $student)
                                <tr>
                                    <td class="text-center">
                                        {{ ($students->currentPage() - 1) * $students->perPage() + $index + 1 }}
                                    </td>
                                    <td>
                                        <strong>{{ $student->formatted_name }}</strong>
                                        <br><small class="text-muted">{{ $student->name }}</small>
                                    </td>
                                    <td>
                                        <a href="mailto:{{ $student->email }}">{{ $student->email }}</a>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info">{{ $student->year_level ?? 'N/A' }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($student->sections->count() > 0)
                                            <span class="badge badge-success" title="{{ $student->sections->count() }} {{ Str::plural('section', $student->sections->count()) }}">
                                                {{ $student->sections->count() }}
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $student->created_at->format('M d, Y') }}</small>
                                        <br>
                                        <small class="text-muted">{{ $student->created_at->diffForHumans() }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <p class="text-muted mb-0">
                            Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} students
                        </p>
                    </div>
                    <div>
                       {{ $students->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if(auth()->user()->hasRole('professor'))
        <!-- Quick Actions Card -->
        <div class="card shadow mt-4 border-secondary">
            <div class="card-header bg-secondary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-bolt"></i> Quick Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('students.create') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-user-plus"></i> Register Single Student
                        </a>
                        <small class="text-muted">Manually register one student at a time</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('students.import.form') }}" class="btn btn-info btn-block">
                            <i class="fas fa-file-csv"></i> Bulk Import via CSV
                        </a>
                        <small class="text-muted">Register multiple students at once</small>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif

<style>
/* Improve pagination styling */
.pagination {
    margin-bottom: 0;
}

.page-link {
    color: #4e73df;
}

.page-item.active .page-link {
    background-color: #4e73df;
    border-color: #4e73df;
}

.page-link:hover {
    color: #2e59d9;
}

/* Hover effect for table rows */
.table-hover tbody tr:hover {
    background-color: #f8f9fc;
    cursor: pointer;
}

/* Badge styling */
.badge {
    font-size: 0.85rem;
    padding: 0.35em 0.65em;
}
</style>
@endsection
