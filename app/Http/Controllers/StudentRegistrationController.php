<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class StudentRegistrationController extends Controller
{
    /**
     * Show form to register a new student (Professor & Admin only)
     */
    public function create()
    {
        return view('students.create');
    }

    /**
     * Store a newly registered student
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'year_level' => ['required', 'string'],
        ]);

        $student = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'year_level' => $request->year_level,
        ]);

        $student->assignRole('student');

        // Audit Trail
        AuditTrail::log(
            action: 'create',
            modelType: 'User',
            modelId: $student->id,
            description: sprintf(
                'Registered new student: %s (%s) - %s',
                $student->name,
                $student->email,
                $student->year_level
            ),
            oldValues: null,
            newValues: [
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'middle_name' => $student->middle_name,
                'email' => $student->email,
                'year_level' => $student->year_level,
            ]
        );

        return redirect()->route('students.index')
            ->with('success', 'Student registered successfully! They can now be enrolled in sections via CSV.');
    }

    /**
     * Show CSV upload form for bulk student registration
     */
    public function importForm()
    {
        return view('students.import');
    }

    /**
     * Import students via CSV
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $path = $request->file('csv_file')->getRealPath();
        $rows = array_map('str_getcsv', file($path));
        $header = array_map('strtolower', array_map('trim', $rows[0]));
        unset($rows[0]);

        $registered = 0;
        $skipped = 0;
        $errors = [];
        $registeredStudents = [];

        foreach ($rows as $index => $row) {
            $data = array_combine($header, $row);

            // Validate row data - UPDATED to use first_name, last_name
            if (empty($data['first_name']) || empty($data['last_name']) || empty($data['email']) || empty($data['password'])) {
                $errors[] = "Row " . ($index + 2) . ": Missing required fields (first_name, last_name, email, password).";
                $skipped++;
                continue;
            }

            // Check if email already exists
            if (User::where('email', $data['email'])->exists()) {
                $errors[] = "Row " . ($index + 2) . ": Email {$data['email']} already exists.";
                $skipped++;
                continue;
            }

            // Create student with first_name, last_name, middle_name
            $student = User::create([
                'first_name' => trim($data['first_name']),
                'last_name' => trim($data['last_name']),
                'middle_name' => !empty($data['middle_name']) ? trim($data['middle_name']) : null,
                'email' => trim($data['email']),
                'password' => Hash::make(trim($data['password'])),
                'year_level' => !empty($data['year_level']) ? trim($data['year_level']) : '1st Year',
            ]);

            $student->assignRole('student');
            $registeredStudents[] = $student->name;
            $registered++;
        }

        // Audit Trail for bulk registration
        if ($registered > 0) {
            AuditTrail::log(
                action: 'create',
                modelType: 'User',
                modelId: null,
                description: sprintf(
                    'Bulk registered %d student(s) via CSV',
                    $registered
                ),
                oldValues: null,
                newValues: ['students_registered' => $registeredStudents]
            );
        }

        $message = "Student registration complete: {$registered} student(s) registered, {$skipped} skipped.";

        return redirect()->route('students.index')
            ->with('success', $message)
            ->with('import_errors', $errors);
    }
}
