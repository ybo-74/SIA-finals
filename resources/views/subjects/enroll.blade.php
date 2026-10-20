@extends('layouts.student')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Enroll in Subjects</h1>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Available Subjects</h6>
        </div>
        <div class="card-body">
            @if($subjects->isEmpty())
                <div class="alert alert-info">No subjects available for enrollment at this time.</div>
            @else
                <form action="{{ route('subjects.enroll.submit') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <p class="text-muted">Select the subjects you want to enroll in:</p>
                        @foreach($subjects as $subject)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="subjects[]" 
                                       value="{{ $subject->id }}" id="subject{{ $subject->id }}"
                                       {{ auth()->user()->subjectsEnrolled->contains($subject->id) ? 'checked disabled' : '' }}>
                                <label class="form-check-label" for="subject{{ $subject->id }}">
                                    <strong>{{ $subject->code }}</strong> - {{ $subject->name }}
                                    @if($subject->professor)
                                        <br><small class="text-muted">Professor: {{ $subject->professor->name }}</small>
                                    @endif
                                    @if(auth()->user()->subjectsEnrolled->contains($subject->id))
                                        <span class="badge bg-success">Already Enrolled</span>
                                    @endif
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <button type="submit" class="btn btn-primary">Enroll in Selected Subjects</button>
                    <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">Cancel</a>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection