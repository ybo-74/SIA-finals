@extends('layouts.student')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4 text-gray-800">My Grades</h2>

    @if($sections->isEmpty())
        <div class="alert alert-info">
            You are not enrolled in any sections yet. Please enroll in sections to view your grades.
        </div>
    @else
        @foreach($sections as $section)
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        {{ $section->name }} - {{ $section->subject->code }}
                    </h6>
                    <small>{{ $section->subject->name }} | Professor: {{ $section->professor->name ?? 'Unassigned' }}</small>
                </div>
                <div class="card-body">
                    <!-- Display grades by term -->
                    <div class="row">
                        @foreach(['Prelim', 'Midterm', 'Finals'] as $term)
                            <div class="col-md-4">
                                <h6 class="
                                    @if($term === 'Prelim') text-success
                                    @elseif($term === 'Midterm') text-info
                                    @else text-danger
                                    @endif border-bottom pb-2">
                                    <i class="fas fa-clipboard-list"></i> {{ $term }}
                                </h6>
                                
                                @php
                                    $termGrades = $section->studentGrades->filter(function($g) use ($term) {
                                        return $g->gradeCriteria && $g->gradeCriteria->term === $term;
                                    });
                                    
                                    $lectureGrades = $termGrades->filter(fn($g) => $g->gradeCriteria->category === 'lecture');
                                    $labGrades = $termGrades->filter(fn($g) => $g->gradeCriteria->category === 'laboratory');
                                @endphp
                                
                                @if($termGrades->isEmpty())
                                    <p class="text-muted small">No grades yet.</p>
                                @else
                                    <!-- Lecture Grades -->
                                    @if($lectureGrades->isNotEmpty())
                                        <div class="mb-2">
                                            <strong class="text-success">Lecture:</strong>
                                            <ul class="list-unstyled ml-3">
                                                @foreach($lectureGrades as $grade)
                                                    <li class="small">
                                                        {{ $grade->gradeCriteria->name }}
                                                        <span class="text-muted">({{ $grade->gradeCriteria->weight }}%)</span>:
                                                        <span class="badge badge-success">{{ number_format($grade->score, 2) }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    
                                    <!-- Lab Grades -->
                                    @if($section->subject->has_lab && $labGrades->isNotEmpty())
                                        <div class="mb-2">
                                            <strong class="text-info">Laboratory:</strong>
                                            <ul class="list-unstyled ml-3">
                                                @foreach($labGrades as $grade)
                                                    <li class="small">
                                                        {{ $grade->gradeCriteria->name }}
                                                        <span class="text-muted">({{ $grade->gradeCriteria->weight }}%)</span>:
                                                        <span class="badge badge-info">{{ number_format($grade->score, 2) }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    
                                    <hr class="my-2">
                                    
                                    <!-- Term Grade -->
                                    @php
                                        // Calculate term grade
                                        $lectureAvg = null;
                                        $labAvg = null;
                                        
                                        if ($lectureGrades->isNotEmpty()) {
                                            $totalWeight = 0;
                                            $weightedSum = 0;
                                            foreach($lectureGrades as $grade) {
                                                $criteria = $grade->gradeCriteria;
                                                $weight = $criteria->weight;
                                                
                                                // Convert raw score to percentage first
                                                $percentage = $criteria->calculatePercentage($grade->score);
                                                
                                                // Then apply weight
                                                $weightedScore = $percentage * ($weight / 100);
                                                
                                                $weightedSum += $weightedScore;
                                                $totalWeight += $weight;
                                            }
                                            $lectureAvg = $totalWeight > 0 ? $weightedSum : null;
                                        }
                                        
                                        if ($labGrades->isNotEmpty()) {
                                            $totalWeight = 0;
                                            $weightedSum = 0;
                                            foreach($labGrades as $grade) {
                                                $criteria = $grade->gradeCriteria;
                                                $weight = $criteria->weight;
                                                
                                                // Convert raw score to percentage first
                                                $percentage = $criteria->calculatePercentage($grade->score);
                                                
                                                // Then apply weight
                                                $weightedScore = $percentage * ($weight / 100);
                                                
                                                $weightedSum += $weightedScore;
                                                $totalWeight += $weight;
                                            }
                                            $labAvg = $totalWeight > 0 ? $weightedSum : null;
                                        }
                                        
                                        // Calculate final term grade
                                        $termGrade = 0;
                                        if ($section->subject->has_lab) {
                                            if ($lectureAvg !== null && $labAvg !== null) {
                                                $termGrade = ($lectureAvg * 0.7) + ($labAvg * 0.3);
                                            } elseif ($lectureAvg !== null) {
                                                $termGrade = $lectureAvg * 0.7;
                                            } elseif ($labAvg !== null) {
                                                $termGrade = $labAvg * 0.3;
                                            }
                                        } else {
                                            $termGrade = $lectureAvg ?? 0;
                                        }
                                    @endphp
                                    
                                    <p class="mb-0">
                                        <strong>{{ $term }} Grade:</strong>
                                        <span class="badge badge-primary" style="font-size: 1rem;">
                                            {{ $termGrade > 0 ? number_format($termGrade, 2) : '--' }}
                                        </span>
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Final Weighted Grade -->
                    <hr class="my-3">
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-primary mb-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-0">
                                            <i class="fas fa-graduation-cap"></i> Final Grade for this Subject
                                        </h5>
                                        <small class="text-muted">
                                            Formula: (Prelim + Midterm + Finals) ÷ 3
                                            @if($section->subject->has_lab)
                                                | Each Term = (Lecture × 70%) + (Lab × 30%)
                                            @else
                                                | Each Term = Lecture (100%)
                                            @endif
                                        </small>
                                    </div>
                                    <div class="text-right">
                                        @if($section->final_grade > 0)
                                            <h2 class="mb-0 
                                                @if($section->final_grade >= 90) text-success
                                                @elseif($section->final_grade >= 80) text-primary
                                                @elseif($section->final_grade >= 70) text-info
                                                @elseif($section->final_grade >= 60) text-warning
                                                @else text-danger
                                                @endif">
                                                {{ number_format($section->final_grade, 2) }}
                                            </h2>
                                            <small class="text-muted">
                                                @if($section->final_grade >= 90) Excellent
                                                @elseif($section->final_grade >= 80) Very Good
                                                @elseif($section->final_grade >= 70) Good
                                                @elseif($section->final_grade >= 60) Fair
                                                @else Needs Improvement
                                                @endif
                                            </small>
                                        @else
                                            <span class="text-muted">Not enough data yet</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Overall Average -->
        @php
            $overallAvg = $sections->where('final_grade', '>', 0)->avg('final_grade');
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
                            <h6 class="text-muted mb-2">Overall Average Across All Subjects</h6>
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
                                        <small class="text-muted">Subjects with A (90+)</small>
                                        <h4 class="mb-0 text-success">
                                            {{ $sections->filter(fn($s) => $s->final_grade >= 90)->count() }}
                                        </h4>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="border-left-primary pl-3">
                                        <small class="text-muted">Subjects with B (80-89)</small>
                                        <h4 class="mb-0 text-primary">
                                            {{ $sections->filter(fn($s) => $s->final_grade >= 80 && $s->final_grade < 90)->count() }}
                                        </h4>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="border-left-info pl-3">
                                        <small class="text-muted">Subjects with C (70-79)</small>
                                        <h4 class="mb-0 text-info">
                                            {{ $sections->filter(fn($s) => $s->final_grade >= 70 && $s->final_grade < 80)->count() }}
                                        </h4>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="border-left-warning pl-3">
                                        <small class="text-muted">Total Subjects Enrolled</small>
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
    @endif
</div>
@endsection
