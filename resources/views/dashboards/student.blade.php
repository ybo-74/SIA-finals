@extends('layouts.student')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4 text-gray-800">Student Dashboard</h2>

    <div class="row mb-4">
        <div class="col-md-12">
            <a href="{{ route('grades.student.view') }}" class="btn btn-primary btn-block mb-2">
                <i class="fas fa-chart-bar"></i> View My Detailed Grades
            </a>
        </div>
    </div>

    @if($sections->isEmpty())
        <div class="alert alert-info">
            You are not enrolled in any sections yet. Your professor will enroll you in their sections.
        </div>
    @else
        <!-- My Enrolled Sections Overview -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-gradient-primary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-book"></i> My Enrolled Section and Subjects
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 30%">Section</th>
                                <th style="width: 25%">Subject</th>
                                <th style="width: 20%">Professor</th>
                                <th style="width: 25%" class="text-center">Current Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sections as $section)
                                <tr>
                                    <td>
                                        <strong>{{ $section->name }}</strong><br>
                                        <small class="text-muted">{{ $section->year_level }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $section->subject->code }}</strong><br>
                                        <small class="text-muted">{{ $section->subject->name }}</small>
                                    </td>
                                    <td>
                                        {{ $section->professor->name ?? 'Unassigned' }}
                                    </td>
                                    <td class="text-center">
                                        @if($section->final_grade > 0)
                                            <h4 class="mb-0 
                                                @if($section->final_grade >= 90) text-success
                                                @elseif($section->final_grade >= 80) text-primary
                                                @elseif($section->final_grade >= 70) text-info
                                                @elseif($section->final_grade >= 60) text-warning
                                                @else text-danger
                                                @endif">
                                                {{ number_format($section->final_grade, 2) }}
                                            </h4>
                                            <small class="text-muted">
                                                @if($section->final_grade >= 90) Excellent
                                                @elseif($section->final_grade >= 80) Very Good
                                                @elseif($section->final_grade >= 70) Good
                                                @elseif($section->final_grade >= 60) Fair
                                                @else Needs Improvement
                                                @endif
                                            </small>
                                        @else
                                            <span class="text-muted">No grades yet</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Overall Performance Card -->
        @php
            $sectionsWithGrades = $sections->filter(fn($s) => $s->final_grade > 0);
            $overallAvg = $sectionsWithGrades->avg('final_grade');
        @endphp
        
        @if($overallAvg)
            <div class="card shadow mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="m-0 font-weight-bold">
                        <i class="fas fa-chart-line"></i> Overall Academic Performance
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <h6 class="text-muted mb-2">Overall Average</h6>
                            <h1 class="display-3 
                                @if($overallAvg >= 90) text-success
                                @elseif($overallAvg >= 80) text-primary
                                @elseif($overallAvg >= 70) text-info
                                @elseif($overallAvg >= 60) text-warning
                                @else text-danger
                                @endif mb-0">
                                {{ number_format($overallAvg, 2) }}
                            </h1>
                            <p class="text-muted">
                                @if($overallAvg >= 90) Excellent Performance
                                @elseif($overallAvg >= 80) Very Good Performance
                                @elseif($overallAvg >= 70) Good Performance
                                @elseif($overallAvg >= 60) Fair Performance
                                @else Needs Improvement
                                @endif
                            </p>
                        </div>
                        <div class="col-md-8">
                            <h6 class="text-primary mb-3">Performance Breakdown</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="border-left-success pl-3">
                                        <small class="text-muted">Sections with A (90+)</small>
                                        <h4 class="mb-0 text-success">
                                            {{ $sectionsWithGrades->filter(fn($s) => $s->final_grade >= 90)->count() }}
                                        </h4>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="border-left-primary pl-3">
                                        <small class="text-muted">Sections with B (80-89)</small>
                                        <h4 class="mb-0 text-primary">
                                            {{ $sectionsWithGrades->filter(fn($s) => $s->final_grade >= 80 && $s->final_grade < 90)->count() }}
                                        </h4>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="border-left-info pl-3">
                                        <small class="text-muted">Sections with C (70-79)</small>
                                        <h4 class="mb-0 text-info">
                                            {{ $sectionsWithGrades->filter(fn($s) => $s->final_grade >= 70 && $s->final_grade < 80)->count() }}
                                        </h4>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="border-left-warning pl-3">
                                        <small class="text-muted">Total Sections Enrolled</small>
                                        <h4 class="mb-0 text-dark">
                                            {{ $sections->count() }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Quick Actions -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-info-circle"></i> Information
                </h6>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <i class="fas fa-check-circle text-success"></i> 
                    You are currently enrolled in <strong>{{ $sections->count() }}</strong> section(s).
                </p>
                <p class="mb-2">
                    <i class="fas fa-graduation-cap text-primary"></i> 
                    Your grades are updated by your professors and administrators.
                </p>
                <p class="mb-0">
                    <i class="fas fa-chart-bar text-info"></i> 
                    Click "View My Detailed Grades" to see complete grade breakdowns.
                </p>
            </div>
        </div>
    @endif
</div>
@endsection
