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

    // Role check methods
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

    // Relationships - Updated for students
    public function internships()
    {
        return $this->hasMany(Internship::class, 'student_id');
    }

    // Relationships - Updated for teachers
    public function supervisedInternships()
    {
        return $this->hasMany(Internship::class, 'teacher_id');
    }

    // Keep old names for backward compatibility (optional)
    public function studentEnrollments()
    {
        return $this->internships();
    }

    public function teachingEnrollments()
    {
        return $this->supervisedInternships();
    }

    public function subjectSections()
    {
        return $this->hasMany(SubjectSection::class, 'teacher_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    public function qrCode()
    {
        return $this->hasOne(StudentQRCode::class, 'student_id');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Scopes
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

    // Accessors
    public function getTotalHoursAttribute()
    {
        return $this->attendances()->sum('hours_worked');
    }

    public function getTotalAttendanceDaysAttribute()
    {
        return $this->attendances()->count();
    }

    // Updated to use internships
    public function getCurrentInternshipAttribute()
    {
        return $this->internships()
            ->where('status', 'active')
            ->with('subject')
            ->first();
    }

    // Keep old name for backward compatibility
    public function getCurrentEnrollmentAttribute()
    {
        return $this->getCurrentInternshipAttribute();
    }

    // Get active internship (alias)
    public function getActiveInternshipAttribute()
    {
        return $this->getCurrentInternshipAttribute();
    }

    // Get all internships for the student
    public function getAllInternshipsAttribute()
    {
        return $this->internships()->with('subject')->get();
    }

    // Get student's total internship hours across all internships
    public function getTotalInternshipHoursAttribute()
    {
        return $this->internships()->sum('total_hours_rendered');
    }

    // Get student's completion status
    public function getHasCompletedInternshipAttribute()
    {
        return $this->internships()->where('status', 'completed')->exists();
    }
}