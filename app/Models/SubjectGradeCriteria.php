<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectGradeCriteria extends Model
{
    use HasFactory;

    protected $table = 'subject_grade_criteria';

    protected $fillable = [
        'subject_id',
        'term',
        'category',
        'name',
        'weight',
        'max_score', // NEW: Maximum score/items for this criteria (e.g., 15 for Quiz 1)
        'admin_only',
        'order'
    ];

    protected $casts = [
        'weight' => 'decimal:4',
        'max_score' => 'decimal:2', // NEW: Supports decimal scores (e.g., 15.5)
        'admin_only' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Relationship to subject
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Relationship to student grades using this criteria
     */
    public function studentGrades()
    {
        return $this->hasMany(StudentGrade::class, 'grade_criteria_id');
    }

    /**
     * NEW: Calculate percentage from raw score
     * Example: 13/15 = 86.67%
     * 
     * @param float $rawScore The raw score entered by professor
     * @return float Percentage (0-100)
     */
    public function calculatePercentage($rawScore)
    {
        if (!$this->max_score || $this->max_score == 0) {
            // Fallback: if no max_score set, treat raw score as percentage
            return $rawScore;
        }
        
        return ($rawScore / $this->max_score) * 100;
    }
    
    /**
     * NEW: Calculate weighted contribution to term grade
     * Example: 13/15 (86.67%) of 6.25% weight = 5.42%
     * 
     * @param float $rawScore The raw score entered by professor
     * @return float Weighted percentage contribution
     */
    public function calculateWeightedScore($rawScore)
    {
        $percentage = $this->calculatePercentage($rawScore);
        return ($percentage / 100) * $this->weight;
    }
    
    /**
     * NEW: Format score display
     * Example: "13/15 (86.67%)"
     * 
     * @param float $rawScore The raw score
     * @return string Formatted display
     */
    public function formatScoreDisplay($rawScore)
    {
        if (!$this->max_score) {
            return number_format($rawScore, 2) . '%';
        }
        
        $percentage = $this->calculatePercentage($rawScore);
        return sprintf('%s/%s (%.2f%%)', 
            number_format($rawScore, 2), 
            number_format($this->max_score, 2),
            $percentage
        );
    }

    /**
     * Get criteria for a specific subject, term, and category
     */
    public static function getForSubjectTermCategory($subjectId, $term, $category)
    {
        return self::where('subject_id', $subjectId)
                   ->where('term', $term)
                   ->where('category', $category)
                   ->orderBy('order')
                   ->orderBy('name')
                   ->get();
    }

    /**
     * Get all criteria for a subject grouped by term and category
     */
    public static function getAllForSubject($subjectId)
    {
        return self::where('subject_id', $subjectId)
                   ->orderBy('term')
                   ->orderBy('category')
                   ->orderBy('order')
                   ->get()
                   ->groupBy(['term', 'category']);
    }

    /**
     * Validate that weights sum to 100 for a category
     */
    public static function validateWeights($subjectId, $term, $category)
    {
        $totalWeight = self::where('subject_id', $subjectId)
                           ->where('term', $term)
                           ->where('category', $category)
                           ->sum('weight');
        
        return abs($totalWeight - 100) < 0.01; // Allow for small floating point differences
    }

    /**
     * Scope to get criteria for a specific term
     */
    public function scopeTerm($query, $term)
    {
        return $query->where('term', $term);
    }

    /**
     * Scope to get criteria for a specific category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to get only professor-editable criteria (not admin-only)
     */
    public function scopeProfessorEditable($query)
    {
        return $query->where('admin_only', false);
    }

    /**
     * Scope to get only admin-only criteria
     */
    public function scopeAdminOnly($query)
    {
        return $query->where('admin_only', true);
    }
}
