<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'year_level',
        'academic_year',
        'semester',
        'subject_id',
        'professor_id'
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function professor()
    {
        return $this->belongsTo(User::class, 'professor_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'subject_student', 'section_id', 'student_id')
                    ->withTimestamps();
    }

    public function studentGrades()
    {
        return $this->hasMany(StudentGrade::class);
    }

    /**
     * Scope to filter by academic year
     */
    public function scopeAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    /**
     * Scope to filter by semester
     */
    public function scopeSemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    /**
     * Get available academic years (for dropdown filters)
     */
    public static function getAcademicYears()
    {
        return self::select('academic_year')
            ->distinct()
            ->orderByDesc('academic_year')
            ->pluck('academic_year');
    }

    /**
     * Get available semesters
     */
    public static function getSemesters()
    {
        return ['1st Semester', '2nd Semester', 'Summer'];
    }
}
