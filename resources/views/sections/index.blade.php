@extends('layouts.professor')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">My Sections</h1>
        <a href="{{ route('sections.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Section
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($sections->isEmpty())
        <div class="alert alert-info">
            You haven't created any sections yet. Click "Create New Section" to get started.
        </div>
    @else
        <div class="row">
            @foreach($sections as $section)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow h-100">
                       <div class="card-header bg-secondary text-white">
                            <h6 class="m-0 font-weight-bold">{{ $section->name }}</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-2">
                                <strong>Subject:</strong> {{ $section->subject->code }} - {{ $section->subject->name }}
                            </p>
                            <p class="mb-2">
                                <strong>Year Level:</strong> 
                                <span class="badge bg-info">{{ $section->year_level }}</span>
                            </p>
                            <p class="mb-0">
                                <strong>Students Enrolled:</strong> {{ $section->students->count() }}
                            </p>
                        </div>
                        <div class="card-footer">
                        <a href="{{ route('sections.show', $section) }}" class="btn btn-sm btn-success btn-block">
                                <i class="fas fa-eye"></i> View Students & Grades
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
