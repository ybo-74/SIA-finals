<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\StudentGrade;
use App\Models\SubjectGradeCriteria;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ExportController extends Controller
{
    /**
     * Export class record to Excel
     * FEATURE #4: Professor can export complete class record with all grades
     * FIXED: Students now sorted alphabetically by surname (last_name)
     */
    public function exportClassRecord(Section $section)
    {
        // Authorization check
        if ($section->professor_id != auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        // Get students with their grades - SORTED ALPHABETICALLY BY SURNAME
        $students = $section->students()
            ->orderBy('last_name', 'asc')
            ->orderBy('first_name', 'asc')
            ->with(['studentGrades' => function($q) use ($section) {
                $q->where('section_id', $section->id)->with('gradeCriteria');
            }])
            ->get();

        // Get all criteria for this subject
        $allCriteria = SubjectGradeCriteria::where('subject_id', $section->subject_id)
            ->orderBy('term')
            ->orderBy('category')
            ->orderBy('order')
            ->get()
            ->groupBy(['term', 'category']);

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator(auth()->user()->name)
            ->setTitle("Class Record - {$section->name}")
            ->setSubject("Grade Record")
            ->setDescription("Complete class record for {$section->subject->name}");

        // Header Information
        $row = 1;
        $sheet->setCellValue("A{$row}", "CLASS RECORD");
        $sheet->mergeCells("A{$row}:F{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row++;
        $sheet->setCellValue("A{$row}", "Subject: {$section->subject->code} - {$section->subject->name}");
        $sheet->mergeCells("A{$row}:F{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);

        $row++;
        $sheet->setCellValue("A{$row}", "Section: {$section->name} | Year Level: {$section->year_level}");
        $sheet->mergeCells("A{$row}:F{$row}");

        $row++;
        $sheet->setCellValue("A{$row}", "Academic Year: {$section->academic_year} | Semester: {$section->semester}");
        $sheet->mergeCells("A{$row}:F{$row}");

        $row++;
        $sheet->setCellValue("A{$row}", "Professor: {$section->professor->name}");
        $sheet->mergeCells("A{$row}:F{$row}");

        $row++;
        $sheet->setCellValue("A{$row}", "Generated: " . now()->format('F d, Y h:i A'));
        $sheet->mergeCells("A{$row}:F{$row}");
        
        $row++;
        $sheet->setCellValue("A{$row}", "Students sorted alphabetically by surname");
        $sheet->mergeCells("A{$row}:F{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setItalic(true)->setSize(9);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row += 2; // Space

        // Build headers
        $colIndex = 0;
        $headerRow = $row;

        // Student info columns
        $sheet->setCellValue($this->getColumnLetter($colIndex) . $row, "#");
        $sheet->getColumnDimension($this->getColumnLetter($colIndex))->setWidth(5);
        $colIndex++;

        $sheet->setCellValue($this->getColumnLetter($colIndex) . $row, "Student Name (Surname, First M.)");
        $sheet->getColumnDimension($this->getColumnLetter($colIndex))->setWidth(30);
        $colIndex++;

        $sheet->setCellValue($this->getColumnLetter($colIndex) . $row, "Email");
        $sheet->getColumnDimension($this->getColumnLetter($colIndex))->setWidth(30);
        $colIndex++;

        // For each term, add criteria columns
        $criteriaColumns = []; // Track which columns belong to which criteria
        $termStartCols = []; // Track starting column for each term

        foreach(['Prelim', 'Midterm', 'Finals'] as $term) {
            $termStartCols[$term] = $colIndex;
            
            foreach(['lecture', 'laboratory'] as $category) {
                if ($category === 'laboratory' && !$section->subject->has_lab) {
                    continue; // Skip lab for lecture-only subjects
                }

                $criteria = $allCriteria->get($term, collect())->get($category, collect());
                
                foreach($criteria as $criteriaItem) {
                    $col = $this->getColumnLetter($colIndex);
                    $sheet->setCellValue("{$col}{$row}", "{$criteriaItem->name}");
                    $sheet->getColumnDimension($col)->setWidth(15);
                    
                    // Store criteria mapping
                    $criteriaColumns[$colIndex] = $criteriaItem->id;
                    
                    // Color code: Green for lecture, Blue for lab, Yellow for admin
                    if ($criteriaItem->admin_only) {
                        $sheet->getStyle("{$col}{$row}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('FFF3CD'); // Yellow
                    } elseif ($category === 'lecture') {
                        $sheet->getStyle("{$col}{$row}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('D4EDDA'); // Green
                    } else {
                        $sheet->getStyle("{$col}{$row}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('D1ECF1'); // Blue
                    }
                    
                    $colIndex++;
                }
            }
            
            // Add term grade column
            $col = $this->getColumnLetter($colIndex);
            $sheet->setCellValue("{$col}{$row}", "{$term} Grade");
            $sheet->getColumnDimension($col)->setWidth(12);
            $sheet->getStyle("{$col}{$row}")->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('FFECB3'); // Light yellow
            $colIndex++;
        }

        // Final Grade column
        $finalGradeColIndex = $colIndex;
        $finalGradeCol = $this->getColumnLetter($colIndex);
        $sheet->setCellValue("{$finalGradeCol}{$row}", "FINAL GRADE");
        $sheet->getColumnDimension($finalGradeCol)->setWidth(15);
        $sheet->getStyle("{$finalGradeCol}{$row}")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFD54F'); // Gold

        // Style header row
        $lastCol = $this->getColumnLetter($colIndex);
        $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")
            ->getFont()->setBold(true);
        $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Add sub-header row with max scores and weights
        $row++;
        $subHeaderRow = $row;
        $colIndex = 0;
        
        // Empty cells for student info columns
        $sheet->setCellValue($this->getColumnLetter($colIndex) . $row, '#');
        $colIndex++;
        $sheet->setCellValue($this->getColumnLetter($colIndex) . $row, 'Max/Weight →');
        $colIndex++;
        $sheet->setCellValue($this->getColumnLetter($colIndex) . $row, '');
        $colIndex++;
        
        // Add max score and weight for each criteria
        foreach($criteriaColumns as $columnIndex => $criteriaId) {
            $criteria = SubjectGradeCriteria::find($criteriaId);
            $col = $this->getColumnLetter($columnIndex);
            
            // Format: 20 items
            //         (17.5%)
            $sheet->setCellValue("{$col}{$row}", "{$criteria->max_score} items\n({$criteria->weight}%)");
            $sheet->getStyle("{$col}{$row}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setWrapText(true);
            $sheet->getStyle("{$col}{$row}")
                ->getFont()->setSize(9)->setBold(true);
        }
        
        // Empty cells for term grades and final grade
        foreach(array_keys($termStartCols) as $term) {
            $col = $this->getColumnLetter($colIndex);
            $sheet->setCellValue("{$col}{$row}", "");
            $colIndex++;
        }
        $sheet->setCellValue("{$finalGradeCol}{$row}", "");
        
        // Style sub-header row
        $sheet->getRowDimension($subHeaderRow)->setRowHeight(30);
        $sheet->getStyle("A{$subHeaderRow}:{$lastCol}{$subHeaderRow}")
            ->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle("A{$subHeaderRow}:{$lastCol}{$subHeaderRow}")
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E9ECEF'); // Light gray
        $sheet->getStyle("A{$subHeaderRow}:{$lastCol}{$subHeaderRow}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle("A{$subHeaderRow}:{$lastCol}{$subHeaderRow}")
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add student data
        $row++;
        $studentStartRow = $row;
        
        foreach($students as $index => $student) {
            $colIndex = 0;
            
            // Student info
            $sheet->setCellValue($this->getColumnLetter($colIndex) . $row, $index + 1);
            $colIndex++;
            
            // Use formatted_name for proper surname, first middle format
            $sheet->setCellValue($this->getColumnLetter($colIndex) . $row, $student->formatted_name);
            $colIndex++;
            
            $sheet->setCellValue($this->getColumnLetter($colIndex) . $row, $student->email);
            $colIndex++;
            
            // Get student's grades
            $studentGrades = $student->studentGrades->keyBy('grade_criteria_id');
            
            // For each criteria column, add grade
            foreach($criteriaColumns as $columnIndex => $criteriaId) {
                $grade = $studentGrades->get($criteriaId);
                $col = $this->getColumnLetter($columnIndex);
                
                if ($grade) {
                    // Format: Just the raw score (e.g., 18)
                    $displayValue = number_format($grade->score, 0);
                    $sheet->setCellValue("{$col}{$row}", $displayValue);
                } else {
                    $sheet->setCellValue("{$col}{$row}", '--');
                }
                
                $sheet->getStyle("{$col}{$row}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
            
            // Calculate and add term grades
            foreach(['Prelim', 'Midterm', 'Finals'] as $term) {
                $termGrade = $this->calculateTermGrade($student, $section, $term);
                $termGradeColIndex = $this->getTermGradeColumnIndex($termStartCols[$term], $allCriteria, $term, $section->subject->has_lab);
                $col = $this->getColumnLetter($termGradeColIndex);
                
                $sheet->setCellValue("{$col}{$row}", $termGrade > 0 ? number_format($termGrade, 2) : '');
                $sheet->getStyle("{$col}{$row}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("{$col}{$row}")
                    ->getFont()->setBold(true);
            }
            
            // Calculate and add final grade
            $finalGrade = $this->calculateFinalGrade($student, $section);
            $sheet->setCellValue("{$finalGradeCol}{$row}", $finalGrade > 0 ? number_format($finalGrade, 2) : '');
            $sheet->getStyle("{$finalGradeCol}{$row}")
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("{$finalGradeCol}{$row}")
                ->getFont()->setBold(true)->setSize(12);
            
            // Color code final grade
            if ($finalGrade >= 90) {
                $sheet->getStyle("{$finalGradeCol}{$row}")->getFont()->getColor()->setRGB('006400'); // Dark green
            } elseif ($finalGrade >= 80) {
                $sheet->getStyle("{$finalGradeCol}{$row}")->getFont()->getColor()->setRGB('0066CC'); // Blue
            } elseif ($finalGrade >= 75) {
                $sheet->getStyle("{$finalGradeCol}{$row}")->getFont()->getColor()->setRGB('FF8C00'); // Orange
            } elseif ($finalGrade > 0) {
                $sheet->getStyle("{$finalGradeCol}{$row}")->getFont()->getColor()->setRGB('DC143C'); // Red
            }
            
            $row++;
        }

        // Add borders to all data
        $sheet->getStyle("A{$studentStartRow}:{$lastCol}" . ($row - 1))
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Add class statistics
        $row += 2;
        $sheet->setCellValue("A{$row}", "CLASS STATISTICS");
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(12);

        $row++;
        $sheet->setCellValue("A{$row}", "Total Students:");
        $sheet->setCellValue("B{$row}", $students->count());

        $row++;
        $studentsWithGrades = $students->filter(function($s) use ($section) {
            return $this->calculateFinalGrade($s, $section) > 0;
        })->count();
        $sheet->setCellValue("A{$row}", "Students with Grades:");
        $sheet->setCellValue("B{$row}", $studentsWithGrades);

        $row++;
        $avgFinal = $students->map(function($s) use ($section) {
            return $this->calculateFinalGrade($s, $section);
        })->filter()->average();
        $sheet->setCellValue("A{$row}", "Class Average:");
        $sheet->setCellValue("B{$row}", $avgFinal ? number_format($avgFinal, 2) : '--');

        // Generate filename
        $filename = "ClassRecord_{$section->subject->code}_{$section->name}_" . now()->format('Ymd_His') . ".xlsx";

        // Save to output
        $writer = new Xlsx($spreadsheet);
        
        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Convert column index (0-based) to Excel column letter (A, B, ..., Z, AA, AB, ...)
     */
    private function getColumnLetter($index)
    {
        $letter = '';
        while ($index >= 0) {
            $letter = chr($index % 26 + 65) . $letter;
            $index = floor($index / 26) - 1;
        }
        return $letter;
    }

    /**
     * Get the column index for term grade column
     */
    private function getTermGradeColumnIndex($startColIndex, $allCriteria, $term, $hasLab)
    {
        $colIndex = $startColIndex;
        $lectureCriteria = $allCriteria->get($term, collect())->get('lecture', collect());
        $labCriteria = $allCriteria->get($term, collect())->get('laboratory', collect());
        
        // Skip all criteria columns
        $colIndex += $lectureCriteria->count();
        if ($hasLab) {
            $colIndex += $labCriteria->count();
        }
        
        return $colIndex;
    }

    private function calculateTermGrade($student, $section, $term)
    {
        $grades = StudentGrade::where('student_id', $student->id)
            ->where('section_id', $section->id)
            ->whereHas('gradeCriteria', function($q) use ($term) {
                $q->where('term', $term);
            })
            ->with('gradeCriteria')
            ->get();

        if ($grades->isEmpty()) {
            return 0;
        }

        $lectureGrades = $grades->filter(fn($g) => $g->gradeCriteria->category === 'lecture');
        $labGrades = $grades->filter(fn($g) => $g->gradeCriteria->category === 'laboratory');

        $lectureAvg = $this->calculateWeightedAverage($lectureGrades);
        $labAvg = $this->calculateWeightedAverage($labGrades);

        if ($section->subject->has_lab) {
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

    private function calculateFinalGrade($student, $section)
    {
        $termGrades = [];
        foreach(['Prelim', 'Midterm', 'Finals'] as $term) {
            $grade = $this->calculateTermGrade($student, $section, $term);
            if ($grade > 0) {
                $termGrades[] = $grade;
            }
        }

        return count($termGrades) > 0 ? array_sum($termGrades) / count($termGrades) : 0;
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
