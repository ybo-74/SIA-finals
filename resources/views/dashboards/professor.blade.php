@extends('layouts.professor')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4 text-gray-800">Professor Dashboard</h2>

    <!-- Filter Card -->
    <div class="card shadow mb-4">
       <div class="card-header py-3 bg-secondary text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-filter"></i> Filter Courses
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('professor.dashboard') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-4">
                        <label for="academic_year" class="form-label">Academic Year</label>
                        <select name="academic_year" id="academic_year" class="form-control">
                            <option value="">All Academic Years</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="semester" class="form-label">Semester</label>
                        <select name="semester" id="semester" class="form-control">
                            <option value="">All Semesters</option>
                            @foreach($semesters as $sem)
                                <option value="{{ $sem }}" {{ request('semester') == $sem ? 'selected' : '' }}>
                                    {{ $sem }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-success mr-2">
                            <i class="fas fa-search"></i> Apply Filters
                        </button>
                        <a href="{{ route('professor.dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Display Current Filter Info -->
    @if(request('academic_year') || request('semester'))
        <div class="alert alert-info alert-dismissible fade show">
            <i class="fas fa-info-circle"></i>
            <strong>Showing filtered results:</strong>
            @if(request('academic_year'))
                Academic Year: <span class="badge badge-primary">{{ request('academic_year') }}</span>
            @endif
            @if(request('semester'))
                Semester: <span class="badge badge-success">{{ request('semester') }}</span>
            @endif
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-md-6">
            <a href="{{ route('sections.index') }}" class="btn btn-success btn-block mb-2">
                <i class="fas fa-list"></i> View My Sections
            </a>
        </div>
        <div class="col-md-6">
            <a href="{{ route('sections.create') }}" class="btn btn-primary btn-block mb-2">
                <i class="fas fa-plus"></i> Create New Section
            </a>
        </div>
    </div>

    @if($subjects->isEmpty())
        <div class="alert alert-warning">
            @if(request('academic_year') || request('semester'))
                No subjects found for the selected filters. Try adjusting your filters or 
                <a href="{{ route('professor.dashboard') }}" class="alert-link">view all courses</a>.
            @else
                You are not assigned to any subjects yet. Please contact the administrator.
            @endif
        </div>
    @else
        <!-- Display Header with Filter Info -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-gradient-secondary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-chalkboard-teacher"></i> 
                    @if(request('academic_year'))
                        Courses for AY {{ request('academic_year') }}
                        @if(request('semester'))
                            - {{ request('semester') }}
                        @endif
                    @else
                        My Subjects - All Academic Years
                    @endif
                </h6>
            </div>
            <div class="card-body">
                <!-- Summary Statistics -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Subjects
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            {{ $subjects->count() }}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-book fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Total Sections
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            {{ $sections->count() }}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Total Students
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            {{ $sections->sum(function($section) { return $section->students->count(); }) }}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Subjects Grid -->
                <div class="row">
                    @foreach($subjects as $subject)
                        @php
                            $sectionsCount = $subject->sections->count();
                            $totalStudents = $subject->sections->sum('students_count');
                        @endphp
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card border-left-success shadow h-100">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        {{ $subject->code }}
                                    </div>
                                    <div class="h5 mb-2 font-weight-bold text-gray-800">
                                        {{ $subject->name }}
                                    </div>
                                    
                                    <div class="mb-2">
                                        <span class="badge badge-primary">
                                            {{ $subject->has_lab ? 'Lecture + Lab' : 'Lecture Only' }}
                                        </span>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="row no-gutters">
                                        <div class="col">
                                            <div class="text-xs mb-1">
                                                <i class="fas fa-users text-success"></i> 
                                                <strong>{{ $sectionsCount }}</strong> Section(s)
                                            </div>
                                            <div class="text-xs">
                                                <i class="fas fa-user-graduate text-info"></i> 
                                                <strong>{{ $totalStudents }}</strong> Student(s)
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if($sectionsCount > 0)
                                        <hr>
                                        <div class="text-xs text-muted mb-2">
                                            <strong>Sections:</strong>
                                        </div>
                                        <div class="mb-2">
                                            @foreach($subject->sections as $section)
                                                <span class="badge badge-secondary mb-1">
                                                    {{ $section->name }}
                                                    <span class="text-white">({{ $section->students_count }})</span>
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                    
                                    <a href="{{ route('sections.index') }}" class="btn btn-sm btn-success mt-2 btn-block">
                                        <i class="fas fa-eye"></i> Manage Sections
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

<script>
// Auto-submit on filter change (optional, remove if you want manual submit only)
document.getElementById('academic_year').addEventListener('change', function() {
    // Uncomment below line to auto-submit on change
    document.getElementById('filterForm').submit();
});

document.getElementById('semester').addEventListener('change', function() {
    // Uncomment below line to auto-submit on change
    document.getElementById('filterForm').submit();
});
</script>
@endsection
