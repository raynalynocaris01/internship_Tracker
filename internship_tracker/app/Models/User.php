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

    // Relationships
    public function studentEnrollments()
    {
        return $this->hasMany(StudentSubjectEnrollment::class, 'student_id');
    }

    public function teachingEnrollments()
    {
        return $this->hasMany(StudentSubjectEnrollment::class, 'teacher_id');
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

    public function getCurrentEnrollmentAttribute()
    {
        return $this->studentEnrollments()
            ->where('status', 'enrolled')
            ->with('subject')
            ->first();
    }
}