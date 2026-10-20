@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 text-gray-800">Subjects</h1>
    <a href="{{ route('subjects.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Subject
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($subjects->isEmpty())
    <div class="alert alert-warning">No subjects found.</div>
@else
    <div class="card shadow">
        <div class="card-body table-responsive">
            <div class="mb-3">
                <input type="text" id="subjectSearchInput" class="form-control" placeholder="Search subjects...">
            </div>

            <table class="table table-bordered table-hover" id="subjectsTable">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Professor</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subjects as $subject)
                        <tr>
                            <td>{{ $subject->name }}</td>
                            <td>{{ $subject->code }}</td>
                            <td>{{ $subject->professor->name ?? 'Unassigned' }}</td>
                            <td>
                                <a href="{{ route('subjects.edit', $subject) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('subjects.destroy', $subject) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Delete this subject?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p id="subjectNoResults" class="text-muted mt-2" style="display: none;">No matching subjects found.</p>
        </div>
    </div>
@endif
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('subjectSearchInput');
    const tableBody = document.querySelector('#subjectsTable tbody');

    if (!searchInput || !tableBody) return;

    searchInput.addEventListener('keyup', function () {
        const query = this.value.toLowerCase();
        const rows = tableBody.querySelectorAll('tr');
        let anyVisible = false;

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const match = text.includes(query);
            row.style.display = match ? '' : 'none';
            if (match) anyVisible = true;
        });

        const noResults = document.getElementById('subjectNoResults');
        if (noResults) {
            noResults.style.display = anyVisible ? 'none' : '';
        }
    });
});
</script>
