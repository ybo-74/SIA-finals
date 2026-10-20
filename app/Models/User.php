<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'name', // Keep for backward compatibility
        'email',
        'password',
        'type',
        'section',
        'year_level'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically set 'name' field when creating with first_name/last_name
        static::creating(function ($user) {
            if (($user->first_name || $user->last_name)) {
                $user->name = static::buildFullName($user->first_name, $user->middle_name, $user->last_name);
            }
        });

        // SQL Server cannot cascade through multiple paths, so what MySQL did with
        // ON DELETE CASCADE / SET NULL for professors is done here instead.
        static::deleting(function ($user) {
            \App\Models\Subject::where('professor_id', $user->id)->update(['professor_id' => null]);

            $sectionIds = \App\Models\Section::where('professor_id', $user->id)->pluck('id');
            if ($sectionIds->isNotEmpty()) {
                \App\Models\StudentGrade::whereIn('section_id', $sectionIds)->delete();
                \App\Models\Section::whereIn('id', $sectionIds)->delete(); // enrollments cascade from sections
            }
        });

        static::updating(function ($user) {
            // Always update name if any name component exists or changed
            if ($user->first_name || $user->last_name || $user->isDirty(['first_name', 'middle_name', 'last_name'])) {
                $user->name = static::buildFullName($user->first_name, $user->middle_name, $user->last_name);
            }
        });
    }

    /**
     * Build full name from components
     */
    private static function buildFullName($firstName, $middleName, $lastName)
    {
        $parts = array_filter([
            trim($firstName ?? ''),
            trim($middleName ?? ''),
            trim($lastName ?? '')
        ], function($part) {
            return $part !== '';
        });
        return implode(' ', $parts);
    }

    /**
     * Get the user's full name (accessor)
     * This maintains backward compatibility with existing code using $user->name
     * Format: "First Middle Last" (e.g., "Juan S. Dela Cruz")
     */
    public function getNameAttribute(): string
    {
        // If the 'name' field is explicitly set in attributes, return it
        if (!empty($this->attributes['name'])) {
            return $this->attributes['name'];
        }

        // If first_name and last_name exist, build name from them
        if (isset($this->attributes['first_name']) || isset($this->attributes['last_name'])) {
            $parts = array_filter([
                $this->attributes['first_name'] ?? '',
                $this->attributes['middle_name'] ?? '',
                $this->attributes['last_name'] ?? ''
            ]);
            return implode(' ', $parts);
        }
        
        return '';
    }

    /**
     * Get formatted name (Last Name, First Name Middle)
     * Useful for formal displays and sorting
     * Format: "Dela Cruz, Juan S."
     */
    public function getFormattedNameAttribute(): string
    {
        if (isset($this->attributes['last_name']) && isset($this->attributes['first_name'])) {
            $formatted = $this->attributes['last_name'] . ', ' . $this->attributes['first_name'];
            if (!empty($this->attributes['middle_name'])) {
                $formatted .= ' ' . $this->attributes['middle_name'];
            }
            return $formatted;
        }
        return $this->name;
    }

    /**
     * Get display name for grading views (Surname, First Middle)
     * This is the primary display format for students
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->formatted_name;
    }

    /**
     * Get initials
     * Useful for avatars
     */
    public function getInitialsAttribute(): string
    {
        $initials = '';
        if (isset($this->attributes['first_name'])) {
            $initials .= strtoupper(substr($this->attributes['first_name'], 0, 1));
        }
        if (isset($this->attributes['last_name'])) {
            $initials .= strtoupper(substr($this->attributes['last_name'], 0, 1));
        }
        
        if (empty($initials) && isset($this->attributes['name'])) {
            return strtoupper(substr($this->attributes['name'], 0, 2));
        }
        
        return $initials;
    }

    /**
     * Subjects this professor teaches.
     */
    public function subjectsTaught()
    {
        return $this->hasMany(\App\Models\Subject::class, 'professor_id');
    }

    /**
     * Subjects this student is enrolled in.
     */
    public function subjectsEnrolled()
    {
        return $this->belongsToMany(\App\Models\Subject::class, 'subject_student', 'student_id', 'subject_id');
    }

    /**
     * Sections created by this professor
     */
    public function sectionsCreated()
    {
        return $this->hasMany(\App\Models\Section::class, 'professor_id');
    }

    /**
     * Sections this student is enrolled in.
     */
    public function sections()
    {
        return $this->belongsToMany(\App\Models\Section::class, 'subject_student', 'student_id', 'section_id')
                    ->withTimestamps();
    }

    /**
     * Student grades
     */
    public function studentGrades()
    {
        return $this->hasMany(StudentGrade::class, 'student_id');
    }

    /**
     * Scope to order by last name, then first name
     */
    public function scopeOrderByName($query)
    {
        return $query->orderBy('last_name')->orderBy('first_name');
    }
}
