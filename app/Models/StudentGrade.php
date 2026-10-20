<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'section_id',
        'subject_id',
        'grade_criteria_id', // Changed from grade_type_id
        'score',
        'entered_by'
    ];

    protected $casts = [
        'score' => 'decimal:4',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Relationship to grade criteria (instead of grade type)
     */
    public function gradeCriteria()
    {
        return $this->belongsTo(SubjectGradeCriteria::class, 'grade_criteria_id');
    }

    public function enteredBy()
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    /**
     * Scope to get grades for a specific term
     */
    public function scopeForTerm($query, $term)
    {
        return $query->whereHas('gradeCriteria', function($q) use ($term) {
            $q->where('term', $term);
        });
    }

    /**
     * Scope to get grades for a specific category
     */
    public function scopeForCategory($query, $category)
    {
        return $query->whereHas('gradeCriteria', function($q) use ($category) {
            $q->where('category', $category);
        });
    }
}
