<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'units',
        'required_hours',
        'semester',
        'school_year',
        'status',
    ];

    protected $casts = [
        'required_hours' => 'integer',
        'units' => 'integer',
        'school_year' => 'integer',
    ];

    // ========== RELATIONSHIPS ==========
    
    /**
     * Sections assigned to this subject (Many-to-Many)
     * Each assignment includes which teacher teaches this subject to that section
     */
    public function sections()
    {
        return $this->belongsToMany(Section::class, 'subject_section')
                    ->withPivot('teacher_id', 'status')
                    ->withTimestamps();
    }

    /**
     * Teachers assigned to teach this subject (Many-to-Many)
     * Each assignment includes which section they teach
     */
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'subject_section', 'subject_id', 'teacher_id')
                    ->withPivot('section_id', 'status')
                    ->withTimestamps();
    }

    /**
     * Internships under this subject
     */
    public function internships()
    {
        return $this->hasMany(Internship::class);
    }

    /**
     * Attendance records for this subject
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // ========== SCOPES ==========
    
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    // ========== ACCESSORS ==========
    
    public function getFullNameAttribute()
    {
        return "{$this->code} - {$this->name}";
    }

    public function getStatusBadgeAttribute()
    {
        return $this->status === 'active' ? 'success' : 'danger';
    }

    // ========== STATISTICS METHODS ==========
    
    public function getTotalActiveInternshipsAttribute()
    {
        return $this->internships()->where('status', 'active')->count();
    }

    public function getTotalCompletedInternshipsAttribute()
    {
        return $this->internships()->where('status', 'completed')->count();
    }

    public function getTotalInternshipsAttribute()
    {
        return $this->internships()->count();
    }

    public function getCompletionRateAttribute()
    {
        $total = $this->total_internships;
        if ($total == 0) return 0;
        
        $completed = $this->total_completed_internships;
        return round(($completed / $total) * 100, 2);
    }

    // ========== TEACHER ASSIGNMENT METHODS ==========
    
    /**
     * Get all teacher assignments for this subject
     * Returns collection with teacher and section info
     */
    public function getTeacherAssignmentsAttribute()
    {
        return $this->sections()
            ->withPivot('teacher_id', 'status')
            ->get()
            ->map(function($section) {
                $teacher = User::find($section->pivot->teacher_id);
                return (object)[
                    'section' => $section,
                    'teacher' => $teacher,
                    'status' => $section->pivot->status
                ];
            });
    }

    /**
     * Check if a teacher is assigned to this subject
     */
    public function hasTeacher($teacherId)
    {
        return $this->teachers()->where('teacher_id', $teacherId)->exists();
    }

    /**
     * Get the teacher assigned to a specific section
     */
    public function getTeacherForSection($sectionId)
    {
        $assignment = $this->sections()
            ->where('section_id', $sectionId)
            ->withPivot('teacher_id')
            ->first();
        
        return $assignment ? User::find($assignment->pivot->teacher_id) : null;
    }
}