<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Subject;
use App\Models\User;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SectionController extends Controller
{
    // Professor: List all sections they created
    public function index()
    {
        $sections = auth()->user()->sectionsCreated()->with('subject')->get();
        return view('sections.index', compact('sections'));
    }

    // Professor: Create section form
    public function create()
    {
        $subjects = auth()->user()->subjectsTaught;
        $yearLevels = ['1st Year', '2nd Year', '3rd Year', '4th Year'];
        $semesters = Section::getSemesters();
        
        // Generate academic year options (current and next 2 years)
        $currentYear = date('Y');
        $academicYears = [];
        for ($i = 0; $i < 3; $i++) {
            $year = $currentYear + $i;
            $academicYears[] = $year . '-' . ($year + 1);
        }
        
        return view('sections.create', compact('subjects', 'yearLevels', 'semesters', 'academicYears'));
    }

    // Professor: Store new section
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'year_level' => 'required|string',
            'academic_year' => 'required|string',
            'semester' => 'required|string|in:1st Semester,2nd Semester,Summer',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $section = Section::create([
            'name' => $request->name,
            'year_level' => $request->year_level,
            'academic_year' => $request->academic_year,
            'semester' => $request->semester,
            'subject_id' => $request->subject_id,
            'professor_id' => auth()->id(),
        ]);

        // Audit Trail
        AuditTrail::log(
            action: 'create',
            modelType: 'Section',
            modelId: $section->id,
            description: sprintf(
                'Created section "%s" for %s (%s, %s)',
                $section->name,
                $section->subject->name,
                $section->academic_year,
                $section->semester
            ),
            oldValues: null,
            newValues: $section->toArray()
        );

        return redirect()->route('sections.index')->with('success', 'Section created successfully!');
    }

    // Professor: View students in section with grades
    public function show(Section $section)
    {
        // Check if professor owns this section
        if ($section->professor_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        // Load students with their grades - SORTED BY SURNAME
        $students = $section->students()
            ->orderBy('last_name', 'asc')
            ->orderBy('first_name', 'asc')
            ->with(['studentGrades' => function($q) use ($section) {
                $q->where('section_id', $section->id)
                  ->with('gradeCriteria');
            }])
            ->get()
            ->map(function($student) use ($section) {
                // Calculate final grade
                $student->final_grade = $this->calculateStudentFinalGrade($student, $section);
                
                // Calculate term grades for display
                $grades = $student->studentGrades;
                foreach(['Prelim', 'Midterm', 'Finals'] as $term) {
                    $termGrade = $this->calculateTermGrade($grades, $term, $section->subject->has_lab);
                    $student->{strtolower($term) . '_grade'} = $termGrade;
                }
                
                return $student;
            });

        return view('sections.show', compact('section', 'students'));
    }

    // Helper: Calculate student final grade
    private function calculateStudentFinalGrade($student, $section)
    {
        $grades = $student->studentGrades;
        
        if ($grades->isEmpty()) {
            return 0;
        }

        $termGrades = [];
        foreach(['Prelim', 'Midterm', 'Finals'] as $term) {
            $termGrade = $this->calculateTermGrade($grades, $term, $section->subject->has_lab);
            if ($termGrade > 0) {
                $termGrades[] = $termGrade;
            }
        }

        return count($termGrades) > 0 ? round(array_sum($termGrades) / count($termGrades), 2) : 0;
    }

    // Helper: Calculate term grade
    private function calculateTermGrade($grades, $term, $hasLab)
    {
        $termGrades = $grades->filter(function($grade) use ($term) {
            return $grade->gradeCriteria && $grade->gradeCriteria->term === $term;
        });

        if ($termGrades->isEmpty()) {
            return 0;
        }

        $lectureGrades = $termGrades->filter(fn($g) => $g->gradeCriteria->category === 'lecture');
        $labGrades = $termGrades->filter(fn($g) => $g->gradeCriteria->category === 'laboratory');

        $lectureAvg = $this->calculateWeightedAverage($lectureGrades);
        $labAvg = $this->calculateWeightedAverage($labGrades);

        if ($hasLab) {
            if ($lectureAvg !== null && $labAvg !== null) {
                return ($lectureAvg * 0.7) + ($labAvg * 0.3);
            } elseif ($lectureAvg !== null) {
                return $lectureAvg * 0.7;
            } elseif ($labAvg !== null) {
                return $labAvg * 0.3;
            }
        } else {
            return $lectureAvg ?? 0;
        }

        return 0;
    }

    // Helper: Calculate weighted average
    private function calculateWeightedAverage($grades)
    {
        if ($grades->isEmpty()) {
            return null;
        }

        $totalWeight = 0;
        $weightedSum = 0;

        foreach ($grades as $grade) {
            $criteria = $grade->gradeCriteria;
            $weight = $criteria->weight;
            
            // Convert raw score to percentage first
            $percentage = $criteria->calculatePercentage($grade->score);
            
            // Then apply the weight
            $weightedScore = $percentage * ($weight / 100);
            
            $weightedSum += $weightedScore;
            $totalWeight += $weight;
        }

        return $totalWeight > 0 ? $weightedSum : null;
    }

    // Professor: Show CSV enrollment form
    public function enrollStudentsForm(Section $section)
    {
        // Check if professor owns this section
        if ($section->professor_id !== auth()->id()) {
            abort(403);
        }

        return view('sections.enroll-students', compact('section'));
    }

    // Professor: Enroll students via CSV
    public function enrollStudentsCsv(Request $request, Section $section)
    {
        // Check if professor owns this section
        if ($section->professor_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $path = $request->file('csv_file')->getRealPath();
        $rows = array_map('str_getcsv', file($path));
        $header = array_map('strtolower', array_map('trim', $rows[0]));
        unset($rows[0]);

        $enrolled = 0;
        $skipped = 0;
        $errors = [];
        $enrolledStudents = [];

        foreach ($rows as $index => $row) {
            $data = array_combine($header, $row);

            // Validate email exists
            $validator = Validator::make($data, [
                'email' => 'required|email|exists:users,email',
            ]);

            if ($validator->fails()) {
                $errors[] = "Row " . ($index + 2) . ": " . implode(', ', $validator->errors()->all());
                $skipped++;
                continue;
            }

            // Find student by email
            $student = User::where('email', $data['email'])->first();

            // Check if user is a student
            if (!$student || !$student->hasRole('student')) {
                $errors[] = "Row " . ($index + 2) . ": Email {$data['email']} is not a student account.";
                $skipped++;
                continue;
            }

            // Check if already enrolled
            if ($section->students()->where('student_id', $student->id)->exists()) {
                $errors[] = "Row " . ($index + 2) . ": {$student->formatted_name} ({$data['email']}) is already enrolled.";
                $skipped++;
                continue;
            }

            // Enroll student
            $section->students()->attach($student->id, [
                'subject_id' => $section->subject_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $enrolledStudents[] = $student->formatted_name;
            $enrolled++;
        }

        // Audit Trail for bulk enrollment
        if ($enrolled > 0) {
            AuditTrail::log(
                action: 'create',
                modelType: 'SectionEnrollment',
                modelId: $section->id,
                description: sprintf(
                    'Enrolled %d student(s) in section "%s" via CSV',
                    $enrolled,
                    $section->name
                ),
                oldValues: null,
                newValues: ['students_enrolled' => $enrolledStudents]
            );
        }

        $message = "Enrollment complete: {$enrolled} student(s) enrolled, {$skipped} skipped.";

        return redirect()->route('sections.show', $section)
            ->with('success', $message)
            ->with('import_errors', $errors);
    }
}
