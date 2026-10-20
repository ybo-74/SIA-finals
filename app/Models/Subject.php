<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'professor_id', 'has_lab'];

    protected $casts = [
        'has_lab' => 'boolean',
    ];

    /**
     * The professor teaching this subject.
     */
    public function professor()
    {
        return $this->belongsTo(User::class, 'professor_id');
    }

    /**
     * Students enrolled in this subject.
     */
    public function students()
    {
        return $this->belongsToMany(User::class, 'subject_student', 'subject_id', 'student_id');
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    /**
     * Grade criteria for this subject
     */
    public function gradeCriteria()
    {
        return $this->hasMany(SubjectGradeCriteria::class);
    }

    /**
     * Get criteria for a specific term and category
     */
    public function getCriteriaFor($term, $category)
    {
        return $this->gradeCriteria()
                    ->where('term', $term)
                    ->where('category', $category)
                    ->orderBy('order')
                    ->orderBy('name')
                    ->get();
    }

    /**
     * Calculate final grade based on subject type and criteria
     * 
     * For subjects with lab:
     * - Lecture: Uses subject-specific lecture criteria
     * - Lab: Uses subject-specific lab criteria
     * - Term Grade = (Lecture × 70%) + (Lab × 30%)
     * 
     * For lecture-only subjects:
     * - Lecture: Uses subject-specific lecture criteria
     * - Term Grade = Lecture (100%)
     * 
     * Final Grade = (Prelim + Midterm + Finals) ÷ 3
     */
    public function calculateTermGrade($lectureAvg, $labAvg = null)
    {
        if ($this->has_lab && $labAvg !== null) {
            // Has both lecture and lab
            return ($lectureAvg * 0.7) + ($labAvg * 0.3);
        } else {
            // Lecture only
            return $lectureAvg;
        }
    }

    /**
     * Calculate final grade from three term grades
     */
    public function calculateFinalGrade($prelimGrade, $midtermGrade, $finalsGrade)
    {
        return ($prelimGrade + $midtermGrade + $finalsGrade) / 3;
    }
}
