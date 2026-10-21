<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\StudentGrade;
use App\Models\SubjectGradeCriteria;
use App\Models\Section;
use App\Models\User;
use App\Models\AuditTrail;

class GradeController extends Controller
{
    /**
     * Professor: View grades input form for a section
     * NOW SUPPORTS: Raw score input (e.g., 13 for 13/15)
     * Students sorted alphabetically by surname
     */
    public function professorGradeInput(Section $section)
    {
        // Check authorization
        if ($section->professor_id != auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        // Get students sorted by surname (last_name), then first_name
        $students = $section->students()
            ->orderBy('last_name', 'asc')
            ->orderBy('first_name', 'asc')
            ->with(['studentGrades' => function($q) use ($section) {
                $q->where('section_id', $section->id)->with('gradeCriteria');
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
        
        // Get subject-specific grade criteria
        $subjectId = $section->subject_id;
        
        // Get professor-editable criteria (admin_only = false)
        $professorCriteria = SubjectGradeCriteria::where('subject_id', $subjectId)
            ->where('admin_only', false)
            ->orderBy('term')
            ->orderBy('category')
            ->orderBy('order')
            ->get()
            ->groupBy(['term', 'category']);
        
        // Get admin-only criteria (for read-only display)
        $adminOnlyCriteria = SubjectGradeCriteria::where('subject_id', $subjectId)
            ->where('admin_only', true)
            ->orderBy('term')
            ->orderBy('category')
            ->orderBy('order')
            ->get()
            ->groupBy(['term', 'category']);

        // Get existing grades
        $existingGrades = StudentGrade::where('section_id', $section->id)
            ->with('gradeCriteria')
            ->get()
            ->groupBy('student_id');

        return view('grades.professor-input', compact(
            'section', 
            'students', 
            'professorCriteria', 
            'adminOnlyCriteria',
            'existingGrades'
        ));
    }

    /**
     * Admin: View Term Exam & All Admin-Only Grades input
     * NOW SUPPORTS: Raw score input (e.g., 13 for 13/15)
     * Students sorted alphabetically by surname
     */
    public function adminGradeInput(Section $section)
    {
        // Get students sorted by surname (last_name), then first_name
        $students = $section->students()
            ->orderBy('last_name', 'asc')
            ->orderBy('first_name', 'asc')
            ->with(['studentGrades' => function($q) use ($section) {
                $q->where('section_id', $section->id)->with('gradeCriteria');
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
        
        $subjectId = $section->subject_id;
        
        // Only admin-only grade criteria
        $adminCriteria = SubjectGradeCriteria::where('subject_id', $subjectId)
            ->where('admin_only', true)
            ->orderBy('term')
            ->orderBy('category')
            ->orderBy('order')
            ->get()
            ->groupBy(['term', 'category']);

        $existingGrades = StudentGrade::where('section_id', $section->id)
            ->whereHas('gradeCriteria', function($q) {
                $q->where('admin_only', true);
            })
            ->with('gradeCriteria')
            ->get()
            ->groupBy('student_id');

        return view('grades.admin-input', compact('section', 'students', 'adminCriteria', 'existingGrades'));
    }

    /**
     * Store/Update grade with RAW SCORE support
     * NEW BEHAVIOR: Accepts raw scores (e.g., 13 for 13/15), calculates percentage automatically
     */
    public function storeGrade(Request $request, Section $section)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'grade_criteria_id' => 'required|exists:subject_grade_criteria,id',
            'score' => 'required|numeric|min:0', // RAW SCORE (e.g., 13)
        ]);

        $criteria = SubjectGradeCriteria::findOrFail($request->grade_criteria_id);
        $student = User::findOrFail($request->student_id);

        // NEW: Validate raw score doesn't exceed max_score
        if ($criteria->max_score && $request->score > $criteria->max_score) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => "Score cannot exceed maximum of {$criteria->max_score}",
                    'max_score' => $criteria->max_score
                ], 422);
            }
            return back()->with('error', "Score cannot exceed maximum of {$criteria->max_score}");
        }

        // Authorization check
        if ($criteria->admin_only && !auth()->user()->hasRole('admin')) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            return back()->with('error', 'You do not have permission to edit this grade type.');
        }

        if (!$criteria->admin_only && !auth()->user()->hasRole(['admin', 'professor'])) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            return back()->with('error', 'Unauthorized action.');
        }

        // Check if grade exists
        $existingGrade = StudentGrade::where([
            'student_id' => $request->student_id,
            'section_id' => $section->id,
            'grade_criteria_id' => $request->grade_criteria_id,
        ])->first();

        $oldScore = $existingGrade ? $existingGrade->score : null;
        $isUpdate = $existingGrade !== null;

        // Store RAW SCORE (not percentage) - e.g., 13 for 13/15
        $grade = StudentGrade::updateOrCreate(
            [
                'student_id' => $request->student_id,
                'section_id' => $section->id,
                'subject_id' => $section->subject_id,
                'grade_criteria_id' => $request->grade_criteria_id,
            ],
            [
                'score' => $request->score, // RAW SCORE: 13
                'entered_by' => auth()->id(),
            ]
        );

        // Calculate percentage for display and audit trail
        $percentage = $criteria->max_score ? 
            ($request->score / $criteria->max_score) * 100 : 
            $request->score;

        // Audit Trail - use formatted name
        AuditTrail::log(
            action: $isUpdate ? 'update' : 'create',
            modelType: 'StudentGrade',
            modelId: $grade->id,
            description: sprintf(
                '%s grade for %s in %s (%s - %s): %s → %s',
                $isUpdate ? 'Updated' : 'Created',
                $student->formatted_name,
                $section->name,
                $criteria->term,
                $criteria->name,
                $criteria->formatScoreDisplay($oldScore ?? 0),
                $criteria->formatScoreDisplay($request->score)
            ),
            oldValues: $isUpdate ? ['score' => $oldScore] : null,
            newValues: ['score' => $request->score]
        );

        // Return JSON for AJAX requests with updated final grade
        if ($request->expectsJson()) {
            $finalGrade = $this->calculateStudentFinalGrade($student, $section);
            
            return response()->json([
                'success' => true,
                'message' => 'Grade saved successfully!',
                'grade' => $grade,
                'raw_score' => $request->score,
                'max_score' => $criteria->max_score,
                'percentage' => round($percentage, 2),
                'display' => $criteria->formatScoreDisplay($request->score),
                'final_grade' => round($finalGrade, 2)
            ]);
        }

        return back()->with('success', 'Grade saved successfully!');
    }

    /**
     * Student: View own grades
     */
    public function studentView()
    {
        $student = auth()->user();
        
        // Get all sections student is enrolled in with their grades
        $sections = $student->sections()
            ->with([
                'subject',
                'professor',
                'studentGrades' => function($q) use ($student) {
                    $q->where('student_id', $student->id)->with('gradeCriteria');
                }
            ])
            ->get()
            ->map(function($section) use ($student) {
                // Calculate final grade for each section
                $section->final_grade = $this->calculateSectionFinalGrade($student, $section);
                
                // Calculate term grades for display
                $grades = StudentGrade::where('student_id', $student->id)
                    ->where('section_id', $section->id)
                    ->with('gradeCriteria')
                    ->get();
                
                foreach(['Prelim', 'Midterm', 'Finals'] as $term) {
                    $termGrade = $this->calculateTermGrade($grades, $term, $section->subject->has_lab);
                    $section->{strtolower($term) . '_grade'} = $termGrade;
                }
                
                return $section;
            });

        return view('grades.student-view-new', compact('sections'));
    }

    /**
     * Delete a grade entry
     */
    public function deleteGrade(Request $request)
    {
        $request->validate([
            'grade_id' => 'required|exists:student_grades,id'
        ]);

        $grade = StudentGrade::with('gradeCriteria', 'student')->findOrFail($request->grade_id);
        
        // Authorization check
        if ($grade->gradeCriteria->admin_only && !auth()->user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $gradeData = [
            'student' => $grade->student->formatted_name,
            'criteria' => $grade->gradeCriteria->name,
            'score' => $grade->score
        ];

        // Audit Trail
        AuditTrail::log(
            action: 'delete',
            modelType: 'StudentGrade',
            modelId: $grade->id,
            description: sprintf(
                'Deleted grade for %s (%s - %s): %s',
                $grade->student->formatted_name,
                $grade->gradeCriteria->term,
                $grade->gradeCriteria->name,
                $grade->gradeCriteria->formatScoreDisplay($grade->score)
            ),
            oldValues: $gradeData,
            newValues: null
        );

        $grade->delete();

        return response()->json([
            'success' => true,
            'message' => 'Grade deleted successfully!'
        ]);
    }

    // Helper methods
    private function calculateStudentFinalGrade(User $student, Section $section)
    {
        $grades = StudentGrade::where('student_id', $student->id)
            ->where('section_id', $section->id)
            ->with('gradeCriteria')
            ->get();

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

    private function calculateSectionFinalGrade(User $student, $section)
    {
        $grades = StudentGrade::where('student_id', $student->id)
            ->where('section_id', $section->id)
            ->with('gradeCriteria')
            ->get();

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

    private function calculateWeightedAverage($grades)
    {
        if ($grades->isEmpty()) {
            return null;
        }

        $totalWeight = 0;
        $weightedSum = 0;

        foreach ($grades as $grade) {
            $weight = $grade->gradeCriteria->weight;
            $percentage = $grade->gradeCriteria->calculatePercentage($grade->score);
            $weightedSum += $percentage * ($weight / 100);
            $totalWeight += $weight;
        }

        // FIXED: Return weighted sum directly (removed incorrect division and multiplication)
        return $totalWeight > 0 ? $weightedSum : null;
    }

    /**
     * FIXED: Professor adds custom grade criteria
     * Changed component_type from dropdown to text input
     * Auto weight distribution logic
     */
    public function addCustomCriteria(Request $request, Section $section)
    {
        // Authorization
        if ($section->professor_id != auth()->id() && !auth()->user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'term' => 'required|in:Prelim,Midterm,Finals',
            'category' => 'required|in:lecture,laboratory',
            'name' => 'required|string|max:255', // FIXED: Direct text input now
            'max_score' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();
        
        try {
            // Extract base name for grouping (e.g., "Quiz" from "Quiz 4")
            $baseName = $this->extractBaseName($request->name);
            
            // Get existing similar criteria in same term/category
            $similarCriteria = SubjectGradeCriteria::where('subject_id', $section->subject_id)
                ->where('term', $request->term)
                ->where('category', $request->category)
                ->where('name', 'LIKE', $baseName . '%')
                ->where('admin_only', false)
                ->get();

            // Calculate new weight distribution
            $newCount = $similarCriteria->count() + 1;
            
            // Get total weight for this component type
            $totalComponentWeight = $this->getDefaultComponentWeight($baseName, $request->category);
            $weightPerItem = $totalComponentWeight / $newCount;

            // Determine insert order
            if ($similarCriteria->isNotEmpty()) {
                // Place after the last similar item
                $insertOrder = $similarCriteria->max('order') + 1;
                
                // FIXED: Shift all items that come after this position
                SubjectGradeCriteria::where('subject_id', $section->subject_id)
                    ->where('term', $request->term)
                    ->where('category', $request->category)
                    ->where('order', '>=', $insertOrder)
                    ->increment('order');
            } else {
                // No similar items, get the max order for this term/category
                $insertOrder = SubjectGradeCriteria::where('subject_id', $section->subject_id)
                    ->where('term', $request->term)
                    ->where('category', $request->category)
                    ->max('order') ?? 0;
                $insertOrder += 1;
            }

            // Update weights for existing similar criteria
            foreach ($similarCriteria as $criteria) {
                $criteria->weight = $weightPerItem;
                $criteria->save();
            }

            // Create new criteria with redistributed weight
            $criteria = SubjectGradeCriteria::create([
                'subject_id' => $section->subject_id,
                'term' => $request->term,
                'category' => $request->category,
                'name' => $request->name,
                'weight' => $weightPerItem,
                'max_score' => $request->max_score,
                'admin_only' => false,
                'order' => $insertOrder,
            ]);

            // Audit Trail
            AuditTrail::log(
                action: 'create',
                modelType: 'SubjectGradeCriteria',
                modelId: $criteria->id,
                description: sprintf(
                    'Added "%s" (%s - %s, max: %s items) at position %d. Weight auto-redistributed: %d items now at %.4f%% each (total: %.2f%%)',
                    $criteria->name,
                    $criteria->term,
                    $criteria->category,
                    $criteria->max_score,
                    $insertOrder,
                    $newCount,
                    $weightPerItem,
                    $totalComponentWeight
                ),
                oldValues: null,
                newValues: [
                    'new_criteria' => $criteria->toArray(),
                    'redistributed_count' => $similarCriteria->count(),
                    'weight_per_item' => $weightPerItem,
                    'insertion_order' => $insertOrder
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => sprintf(
                    'Added "%s" (max: %s items) successfully! Weights auto-redistributed: %d items now at %.4f%% each. Item placed after similar criteria.',
                    $criteria->name,
                    $criteria->max_score,
                    $newCount,
                    $weightPerItem
                ),
                'criteria' => $criteria,
                'redistributed_count' => $similarCriteria->count()
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to add criteria: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extract base name from criteria name (e.g., "Quiz" from "Quiz 4")
     */
    private function extractBaseName($name)
    {
        // Remove numbers and trim
        $baseName = preg_replace('/\d+/', '', $name);
        $baseName = trim($baseName);
        
        // If empty after removing numbers, use first word
        if (empty($baseName)) {
            $words = explode(' ', $name);
            $baseName = $words[0];
        }
        
        return $baseName;
    }

    /**
     * Get default weight allocation for component types
     */
    private function getDefaultComponentWeight($componentType, $category)
    {
        // Default weights based on typical grading composition
        $defaults = [
            'lecture' => [
                'Quiz' => 25,
                'Assignment' => 10,
                'CPA' => 25,
                'Classroom Activity' => 25,
                'Class Participation' => 25,
                'Term Exam' => 40,
                'Project' => 10,
                'Seatwork' => 15,
                'Recitation' => 20,
            ],
            'laboratory' => [
                'Lab Activity' => 70,
                'Hands-on Exam' => 30,
                'Project' => 10,
                'Lab Exercise' => 25,
                'Lab Report' => 15,
            ]
        ];

        return $defaults[$category][$componentType] ?? 10; // Default 10% if not specified
    }

    /**
     * Update max_score for existing criteria
     */
    public function updateMaxScore(Request $request, SubjectGradeCriteria $criteria)
    {
        $request->validate(['max_score' => 'required|numeric|min:1']);

        // Authorization check
        $section = Section::where('subject_id', $criteria->subject_id)
            ->where('professor_id', auth()->id())
            ->first();

        if (!$section && !auth()->user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($criteria->admin_only && !auth()->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot edit admin-only criteria.'
            ], 403);
        }

        $oldMaxScore = $criteria->max_score;
        $criteria->max_score = $request->max_score;
        $criteria->save();

        // Audit Trail
        AuditTrail::log(
            action: 'update',
            modelType: 'SubjectGradeCriteria',
            modelId: $criteria->id,
            description: sprintf(
                'Updated max score for "%s" (%s - %s): %s → %s items',
                $criteria->name,
                $criteria->term,
                $criteria->category,
                $oldMaxScore ?? 'not set',
                $criteria->max_score
            ),
            oldValues: ['max_score' => $oldMaxScore],
            newValues: ['max_score' => $criteria->max_score]
        );

        return response()->json([
            'success' => true,
            'message' => 'Maximum score updated successfully!',
            'criteria' => $criteria,
        ]);
    }

    /**
     * Professor can delete custom grade criteria
     * Only if no grades have been entered for this criteria yet
     */
    public function deleteCriteria(SubjectGradeCriteria $criteria)
    {
        // Authorization check
        $section = Section::where('subject_id', $criteria->subject_id)
            ->where('professor_id', auth()->id())
            ->first();

        if (!$section && !auth()->user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if criteria is admin-only (professors can't delete admin criteria)
        if ($criteria->admin_only && !auth()->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete admin-only criteria.'
            ], 403);
        }

        // Check if any grades have been entered for this criteria
        $gradesCount = StudentGrade::where('grade_criteria_id', $criteria->id)->count();
        
        if ($gradesCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete criteria. {$gradesCount} grade(s) have already been entered."
            ], 400);
        }

        $criteriaData = $criteria->toArray();

        // Audit Trail
        AuditTrail::log(
            action: 'delete',
            modelType: 'SubjectGradeCriteria',
            modelId: $criteria->id,
            description: sprintf(
                'Deleted grade criteria: %s (%s - %s, max: %s items)',
                $criteria->name,
                $criteria->term,
                $criteria->category,
                $criteria->max_score ?? 'not set'
            ),
            oldValues: $criteriaData,
            newValues: null
        );

        $criteria->delete();

        return response()->json([
            'success' => true,
            'message' => 'Criteria deleted successfully!'
        ]);
    }
}
