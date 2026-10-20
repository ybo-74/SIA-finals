@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 text-gray-800">
        <i class="fas fa-history"></i> Audit Trail
    </h1>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>
</div>

<!-- Filter Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-primary text-white">
        <h6 class="m-0 font-weight-bold">
            <i class="fas fa-filter"></i> Filter Audit Logs
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('audit.index') }}">
            <div class="row">
                <div class="col-md-3">
                    <label for="role" class="form-label">User Role</label>
                    <select name="role" id="role" class="form-control">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>
                                {{ ucfirst($role) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="action" class="form-label">Action</label>
                    <select name="action" id="action" class="form-control">
                        <option value="">All Actions</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                {{ ucfirst($action) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="model" class="form-label">Model Type</label>
                    <select name="model" id="model" class="form-control">
                        <option value="">All Models</option>
                        @foreach($models as $model)
                            <option value="{{ $model }}" {{ request('model') == $model ? 'selected' : '' }}>
                                {{ $model }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="search" class="form-label">Search Description</label>
                    <input type="text" 
                           name="search" 
                           id="search" 
                           class="form-control" 
                           value="{{ request('search') }}"
                           placeholder="Search...">
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Date From</label>
                    <input type="date" 
                           name="date_from" 
                           id="date_from" 
                           class="form-control"
                           value="{{ request('date_from') }}">
                </div>

                <div class="col-md-3">
                    <label for="date_to" class="form-label">Date To</label>
                    <input type="date" 
                           name="date_to" 
                           id="date_to" 
                           class="form-control"
                           value="{{ request('date_to') }}">
                </div>

                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">
                        <i class="fas fa-search"></i> Apply Filters
                    </button>
                    <a href="{{ route('audit.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Statistics Card -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Logs</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $logs->total() }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Creates</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                    {{ \App\Models\AuditTrail::where('action', 'create')->count() }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Updates</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                    {{ \App\Models\AuditTrail::where('action', 'update')->count() }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Deletes</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                    {{ \App\Models\AuditTrail::where('action', 'delete')->count() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Audit Logs Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            Activity Logs ({{ $logs->total() }} total)
        </h6>
    </div>
    <div class="card-body">
        @if($logs->isEmpty())
            <div class="alert alert-info mb-0">
                No audit logs found matching your filters.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 12%">Date/Time</th>
                            <th style="width: 15%">User</th>
                            <th style="width: 8%">Role</th>
                            <th style="width: 8%">Action</th>
                            <th style="width: 12%">Model</th>
                            <th style="width: 30%">Description</th>
                            <th style="width: 10%">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td class="text-center">{{ $log->id }}</td>
                                <td>
                                    <small>{{ $log->created_at->format('M d, Y') }}</small><br>
                                    <small class="text-muted">{{ $log->created_at->format('g:i A') }}</small>
                                </td>
                                <td>
                                    <strong>{{ $log->user_name }}</strong><br>
                                    <small class="text-muted">{{ $log->user->email ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <span class="badge 
                                        @if($log->user_role == 'admin') badge-danger
                                        @elseif($log->user_role == 'professor') badge-success
                                        @else badge-info
                                        @endif">
                                        {{ ucfirst($log->user_role) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge 
                                        @if($log->action == 'create') badge-success
                                        @elseif($log->action == 'update') badge-warning
                                        @elseif($log->action == 'delete') badge-danger
                                        @else badge-secondary
                                        @endif">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $log->model_type }}</small><br>
                                    <small>ID: {{ $log->model_id ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <small>{{ \Str::limit($log->description, 80) }}</small>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-info" 
                                            data-toggle="modal" 
                                            data-target="#logModal{{ $log->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modals for Log Details -->
@foreach($logs as $log)
    <div class="modal fade" id="logModal{{ $log->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Audit Log Details - #{{ $log->id }}</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>User:</strong> {{ $log->user_name }}<br>
                            <strong>Role:</strong> {{ ucfirst($log->user_role) }}<br>
                            <strong>Action:</strong> {{ ucfirst($log->action) }}
                        </div>
                        <div class="col-md-6">
                            <strong>Model:</strong> {{ $log->model_type }}<br>
                            <strong>Model ID:</strong> {{ $log->model_id ?? 'N/A' }}<br>
                            <strong>Date:</strong> {{ $log->created_at->format('M d, Y g:i A') }}
                        </div>
                    </div>

                    <hr>

                    <h6 class="font-weight-bold">Description:</h6>
                    <p>{{ $log->description }}</p>

                    @if($log->old_values)
                        <h6 class="font-weight-bold text-danger">Old Values:</h6>
                        <pre class="bg-light p-3 rounded">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                    @endif

                    @if($log->new_values)
                        <h6 class="font-weight-bold text-success">New Values:</h6>
                        <pre class="bg-light p-3 rounded">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                    @endif

                    @if($log->ip_address || $log->user_agent)
                        <hr>
                        <h6 class="font-weight-bold">Technical Details:</h6>
                        <small class="text-muted">
                            <strong>IP Address:</strong> {{ $log->ip_address ?? 'N/A' }}<br>
                            <strong>User Agent:</strong> {{ $log->user_agent ?? 'N/A' }}
                        </small>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection
