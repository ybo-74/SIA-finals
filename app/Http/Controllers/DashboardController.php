<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\User;
use App\Models\StudentGrade;
use App\Models\Section;

class DashboardController extends Controller
{
    /**
     * Admin dashboard: Student rankings by final grade with filters
     */
    public function admin(Request $request)
    {
        // Get filter parameters
        $academicYear = $request->get('academic_year');
        $semester = $request->get('semester');
        $yearLevel = $request->get('year_level');

        // Get sections with filters
        $sectionsQuery = Section::with('subject', 'professor');
        
        if ($academicYear) {
            $sectionsQuery->where('academic_year', $academicYear);
        }
        
        if ($semester) {
            $sectionsQuery->where('semester', $semester);
        }
        
        if ($yearLevel) {
            $sectionsQuery->where('year_level', $yearLevel);
        }
        
        $sections = $sectionsQuery->get();
        
        // Get section IDs for filtering students
        $sectionIds = $sections->pluck('id');

        // Get students enrolled in filtered sections
        if ($sectionIds->isNotEmpty()) {
            $studentsQuery = User::role('student')
                ->whereHas('sections', function($q) use ($sectionIds) {
                    $q->whereIn('sections.id', $sectionIds);
                })
                ->with(['sections' => function($q) use ($sectionIds) {
                    $q->whereIn('sections.id', $sectionIds)
                      ->with(['studentGrades.gradeCriteria', 'subject']);
                }]);
            
            // Apply year level filter if specified
            if ($yearLevel) {
                $studentsQuery->where('year_level', $yearLevel);
            }
            
            $students = $studentsQuery->get()
                ->map(function($student) {
                    $student->final_grade = $this->calculateStudentFinalGrade($student);
                    return $student;
                })
                ->sortByDesc('final_grade')
                ->take(50); // Top 50 students
        } else {
            // No filters - show all students
            $studentsQuery = User::role('student')
                ->with(['sections.studentGrades.gradeCriteria', 'sections.subject']);
            
            // Apply year level filter if specified
            if ($yearLevel) {
                $studentsQuery->where('year_level', $yearLevel);
            }
            
            $students = $studentsQuery->get()
                ->map(function($student) {
                    $student->final_grade = $this->calculateStudentFinalGrade($student);
                    return $student;
                })
                ->sortByDesc('final_grade')
                ->take(50);
        }

        $subjects = Subject::all();
        
        // Get available academic years for filter dropdown
        $academicYears = Section::getAcademicYears();
        
        // Get available year levels for filter dropdown
        $yearLevels = ['1st Year', '2nd Year', '3rd Year', '4th Year'];

        return view('dashboards.admin', compact('subjects', 'students', 'sections', 'academicYears', 'yearLevels'));
    }

    /**
     * Professor dashboard with school year filter
     * FIXED: Shows all assigned subjects, even those without sections
     */
    public function professor(Request $request)
    {
        $professorId = auth()->id();
        
        // Get filter parameters
        $academicYear = $request->get('academic_year');
        $semester = $request->get('semester');
        
        // Build query for sections
        $sectionsQuery = Section::where('professor_id', $professorId)
            ->with(['subject', 'students']);
        
        // Apply filters if provided
        if ($academicYear) {
            $sectionsQuery->where('academic_year', $academicYear);
        }
        
        if ($semester) {
            $sectionsQuery->where('semester', $semester);
        }
        
        $sections = $sectionsQuery->get();
        
        // FIXED: Get ALL subjects assigned to professor, not just those with sections
        $subjectsQuery = Subject::where('professor_id', $professorId);
        
        // If filters are applied, only show subjects that have sections matching those filters
        if ($academicYear || $semester) {
            $subjectIds = $sections->pluck('subject_id')->unique();
            $subjectsQuery->whereIn('id', $subjectIds);
        }
        
        $subjects = $subjectsQuery->with(['sections' => function($q) use ($professorId, $academicYear, $semester) {
            $q->where('professor_id', $professorId);
            
            if ($academicYear) {
                $q->where('academic_year', $academicYear);
            }
            
            if ($semester) {
                $q->where('semester', $semester);
            }
            
            $q->withCount('students');
        }])->get();
        
        // Get available academic years for filter dropdown
        $academicYears = Section::where('professor_id', $professorId)
            ->select('academic_year')
            ->distinct()
            ->orderByDesc('academic_year')
            ->pluck('academic_year');
        
        // Get available semesters for filter dropdown
        $semesters = Section::getSemesters();
        
        return view('dashboards.professor', compact('subjects', 'sections', 'academicYears', 'semesters', 'academicYear', 'semester'));
    }

    /**
     * Student dashboard: Simple list of enrolled sections with grades
     * NO RANKINGS OR LEADERBOARDS
     */
    public function student()
    {
        $student = auth()->user();
        
        // Get all sections student is enrolled in with their grades
        $sections = $student->sections()
            ->with([
                'subject',
                'professor',
                'studentGrades' => function($q) use ($student) {
                    $q->where('student_id', $student->id)->with('gradeCriteria'); // FIXED
                }
            ])
            ->get()
            ->map(function($section) use ($student) {
                // Calculate final grade for each section
                $section->final_grade = $this->calculateSectionFinalGrade($student, $section);
                return $section;
            });

        return view('dashboards.student', compact('sections'));
    }

    private function calculateStudentFinalGrade(User $student)
    {
        $sections = $student->sections;
        
        if ($sections->isEmpty()) {
            return 0;
        }

        $totalGrade = 0;
        $sectionCount = 0;

        foreach ($sections as $section) {
            $sectionGrade = $this->calculateSectionFinalGrade($student, $section);
            if ($sectionGrade > 0) {
                $totalGrade += $sectionGrade;
                $sectionCount++;
            }
        }

        return $sectionCount > 0 ? round($totalGrade / $sectionCount, 2) : 0;
    }

    private function calculateSectionFinalGrade(User $student, $section)
    {
        $grades = StudentGrade::where('student_id', $student->id)
            ->where('section_id', $section->id)
            ->with('gradeCriteria') // FIXED
            ->get();

        if ($grades->isEmpty()) {
            return 0;
        }

        // Calculate per-term grades
        $termGrades = [];
        foreach(['Prelim', 'Midterm', 'Finals'] as $term) {
            $termGrade = $this->calculateTermGrade($grades, $term, $section->subject->has_lab);
            if ($termGrade > 0) {
                $termGrades[] = $termGrade;
            }
        }

        // Final grade = average of term grades
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
}
