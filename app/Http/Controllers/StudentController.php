<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of students with pagination and search
     * Now supports:
     * - Customizable pagination (10, 25, 50, 100 per page)
     * - Search by name or email
     * - Sorting by surname (last_name) alphabetically
     */
    public function index(Request $request)
    {
        $query = User::role('student');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  // Also search in concatenated name for backward compatibility
                  ->orWhereRaw("CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) LIKE ?", ["%{$search}%"]);
            });
        }

        // Sorting - DEFAULT: Sort by last_name, then first_name
        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');

        switch ($sortBy) {
            case 'name':
                // Sort by surname (last_name), then first_name
                $query->orderBy('last_name', $sortDirection)
                      ->orderBy('first_name', $sortDirection);
                break;
            case 'email':
                $query->orderBy('email', $sortDirection);
                break;
            case 'year_level':
                $query->orderBy('year_level', $sortDirection);
                break;
            case 'created_at':
                $query->orderBy('created_at', $sortDirection);
                break;
            default:
                // Default: alphabetical by surname
                $query->orderBy('last_name', 'asc')
                      ->orderBy('first_name', 'asc');
        }

        // Pagination - customizable per page (default 25)
        $perPage = $request->get('per_page', 25);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 25; // Validate per_page value
        
        $students = $query->with('sections')->paginate($perPage)->appends($request->except('page'));

        // Get statistics
        $totalStudents = User::role('student')->count();
        $enrolledStudents = User::role('student')
            ->whereHas('sections')
            ->count();
        $notEnrolledStudents = $totalStudents - $enrolledStudents;

        return view('students.index', compact(
            'students',
            'totalStudents',
            'enrolledStudents',
            'notEnrolledStudents'
        ));
    }
}
