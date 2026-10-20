@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 text-gray-800">Professors</h1>
    <a href="{{ route('professors.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Register New Professor
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
@endif

@if($professors->isEmpty())
    <div class="alert alert-warning">No professors found.</div>
@else
    <div class="card shadow">
        <div class="card-body">
            <div class="mb-3">
                <input type="text" id="searchInput" class="form-control" placeholder="Search professors...">
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="professorsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subjects Assigned</th>
                            <th>Sections Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($professors as $professor)
                            <tr>
                                <td><strong>{{ $professor->name }}</strong></td>
                                <td>{{ $professor->email }}</td>
                                <td>{{ $professor->subjectsTaught->count() }}</td>
                                <td>{{ $professor->sectionsCreated->count() }}</td>
                                <td>
                                    <a href="{{ route('professors.edit', $professor) }}" 
                                       class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('professors.destroy', $professor) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this professor? This will affect their sections and subjects.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.querySelector('#professorsTable tbody');

    if (!searchInput || !tableBody) return;

    searchInput.addEventListener('keyup', function () {
        const query = this.value.toLowerCase();
        const rows = tableBody.querySelectorAll('tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });
});
</script>
@endsection
