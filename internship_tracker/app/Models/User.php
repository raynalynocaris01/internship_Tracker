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
        'teacher_id',  // Changed from employee_id
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
    public function internships()
    {
        return $this->hasMany(Internship::class, 'student_id');
    }

    public function supervisedInternships()
    {
        return $this->hasMany(Internship::class, 'teacher_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'student_id');
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

    // Accessor for displaying teacher ID
    public function getTeacherIdFormattedAttribute()
    {
        return $this->teacher_id ? 'TCH-' . $this->teacher_id : null;
    }

    // Accessor for displaying student ID
    public function getStudentIdFormattedAttribute()
    {
        return $this->student_id ? 'STU-' . $this->student_id : null;
    }
}