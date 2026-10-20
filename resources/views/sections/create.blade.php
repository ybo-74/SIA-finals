@extends('layouts.professor')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Create New Section</h1>
        <a href="{{ route('sections.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('sections.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Section Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   placeholder="e.g., Section A, BSIT-1A" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                                @foreach($yearLevels as $level)
                                    <option value="{{ $level }}" {{ old('year_level') == $level ? 'selected' : '' }}>
                                        {{ $level }}
                                    </option>
                                @endforeach
                            </select>
                            @error('year_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="academic_year" class="form-label">
                                Academic Year <span class="text-danger">*</span>
                            </label>
                            <select class="form-control @error('academic_year') is-invalid @enderror" 
                                    id="academic_year" 
                                    name="academic_year" 
                                    required>
                                <option value="">-- Select Academic Year --</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year }}" {{ old('academic_year') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="semester" class="form-label">
                                Semester <span class="text-danger">*</span>
                            </label>
                            <select class="form-control @error('semester') is-invalid @enderror" 
                                    id="semester" 
                                    name="semester" 
                                    required>
                                <option value="">-- Select Semester --</option>
                                @foreach($semesters as $sem)
                                    <option value="{{ $sem }}" {{ old('semester') == $sem ? 'selected' : '' }}>
                                        {{ $sem }}
                                    </option>
                                @endforeach
                            </select>
                            @error('semester')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="subject_id" class="form-label">
                        Subject <span class="text-danger">*</span>
                    </label>
                    <select class="form-control @error('subject_id') is-invalid @enderror" 
                            id="subject_id" 
                            name="subject_id" 
                            required>
                        <option value="">-- Select Subject --</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->code }} - {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('subject_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if($subjects->isEmpty())
                        <small class="text-danger">
                            You need to be assigned to a subject first before creating sections.
                        </small>
                    @endif
                </div>

                <hr class="my-4">

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> <strong>Note:</strong>
                    After creating the section, you can enroll students via CSV upload.
                </div>

                <div class="mt-4">
                    <button type="submit" 
                            class="btn btn-success" 
                            {{ $subjects->isEmpty() ? 'disabled' : '' }}>
                        <i class="fas fa-save"></i> Create Section
                    </button>
                    <a href="{{ route('sections.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
