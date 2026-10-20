<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditTrail;

class AuditTrailController extends Controller
{
    /**
     * Display audit trail logs (Admin only)
     */
    public function index(Request $request)
    {
        $query = AuditTrail::with('user')->orderBy('created_at', 'desc');

        // Filter by user role
        if ($request->filled('role')) {
            $query->where('user_role', $request->role);
        }

        // Filter by action type
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by model type
        if ($request->filled('model')) {
            $query->where('model_type', $request->model);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by description
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs = $query->paginate(50);

        // Get unique values for filters
        $roles = AuditTrail::select('user_role')->distinct()->pluck('user_role');
        $actions = AuditTrail::select('action')->distinct()->pluck('action');
        $models = AuditTrail::select('model_type')->distinct()->pluck('model_type');

        return view('audit.index', compact('logs', 'roles', 'actions', 'models'));
    }

    /**
     * Show detailed audit trail entry
     */
    public function show(AuditTrail $audit)
    {
        return view('audit.show', compact('audit'));
    }
}
