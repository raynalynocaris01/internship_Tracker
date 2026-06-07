<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'student_id',
        'teacher_id',
        'department',
        'course',
        'year_level',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // ========== ROLE CHECK METHODS ==========
    
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    // ========== STUDENT RELATIONSHIPS ==========
    
    public function internships()
    {
        return $this->hasMany(Internship::class, 'student_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    public function qrCode()
    {
        return $this->hasOne(StudentQRCode::class, 'student_id');
    }

    // ========== TEACHER RELATIONSHIPS ==========
    
    /**
     * Internships supervised by this teacher
     */
    public function supervisedInternships()
    {
        return $this->hasMany(Internship::class, 'teacher_id');
    }

    /**
     * Subjects this teacher teaches (Many-to-Many)
     * Each assignment includes which section they teach
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_section', 'teacher_id', 'subject_id')
                    ->withPivot('section_id', 'status')
                    ->withTimestamps();
    }

    /**
     * Sections this teacher is assigned to (Many-to-Many)
     * Each assignment includes which subject they teach
     */
    public function teachingSections()
    {
        return $this->belongsToMany(Section::class, 'subject_section', 'teacher_id', 'section_id')
                    ->withPivot('subject_id', 'status')
                    ->withTimestamps();
    }

    /**
     * Subject section assignments (pivot)
     */
    public function subjectSections()
    {
        return $this->hasMany(SubjectSection::class, 'teacher_id');
    }

    // ========== COMMON RELATIONSHIPS ==========
    
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // ========== SCOPES ==========
    
    public function scopeStudents($query)
    {
        return $query->where('role', 'student');
    }

    public function scopeTeachers($query)
    {
        return $query->where('role', 'teacher');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    // ========== STUDENT ACCESSORS ==========
    
    public function getTotalHoursAttribute()
    {
        return $this->attendances()->sum('hours_worked');
    }

    public function getTotalAttendanceDaysAttribute()
    {
        return $this->attendances()->count();
    }

    public function getActiveInternshipAttribute()
    {
        return $this->internships()
            ->where('status', 'active')
            ->with('subject')
            ->first();
    }

    public function getAllInternshipsAttribute()
    {
        return $this->internships()->with('subject')->get();
    }

    public function getTotalInternshipHoursAttribute()
    {
        return $this->internships()->sum('total_hours_rendered');
    }

    public function getHasCompletedInternshipAttribute()
    {
        return $this->internships()->where('status', 'completed')->exists();
    }

    // ========== TEACHER ACCESSORS ==========
    
    /**
     * Get all teaching assignments for this teacher
     * Returns collection with subject and section info
     */
    public function getTeachingAssignmentsAttribute()
    {
        return $this->subjects()
            ->withPivot('section_id', 'status')
            ->get()
            ->map(function($subject) {
                $section = Section::find($subject->pivot->section_id);
                return (object)[
                    'subject' => $subject,
                    'section' => $section,
                    'status' => $subject->pivot->status
                ];
            });
    }

    /**
     * Get all sections this teacher is assigned to
     */
    public function getAssignedSectionsAttribute()
    {
        return $this->teachingSections()->get();
    }

    /**
     * Get all subjects this teacher teaches
     */
    public function getTaughtSubjectsAttribute()
    {
        return $this->subjects()->get();
    }

    /**
     * Check if teacher is assigned to a specific subject
     */
    public function teachesSubject($subjectId)
    {
        return $this->subjects()->where('subject_id', $subjectId)->exists();
    }

    /**
     * Check if teacher is assigned to a specific section
     */
    public function teachesInSection($sectionId)
    {
        return $this->teachingSections()->where('section_id', $sectionId)->exists();
    }

    /**
     * Get total number of students under this teacher's supervision
     */
    public function getTotalStudentsAttribute()
    {
        return $this->supervisedInternships()
            ->where('status', 'active')
            ->distinct('student_id')
            ->count('student_id');
    }

    /**
     * Get total active internships under this teacher
     */
    public function getActiveInternshipsCountAttribute()
    {
        return $this->supervisedInternships()->where('status', 'active')->count();
    }
}