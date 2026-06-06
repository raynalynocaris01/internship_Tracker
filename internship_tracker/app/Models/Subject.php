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

    // Relationships
    public function sections()
    {
        return $this->belongsToMany(Section::class, 'subject_section')
                    ->withPivot('teacher_id', 'status')
                    ->withTimestamps();
    }

    public function teachers()
    {
        return $this->belongsToMany(User::class, 'subject_section', 'subject_id', 'teacher_id')
                    ->withPivot('section_id', 'status')
                    ->withTimestamps();
    }

    public function enrollments()
    {
        return $this->hasMany(StudentSubjectEnrollment::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return "{$this->code} - {$this->name}";
    }

    public function getStatusBadgeAttribute()
    {
        return $this->status === 'active' ? 'success' : 'danger';
    }

    // Helper Methods
    public function getTotalEnrolledStudentsAttribute()
    {
        return $this->enrollments()->where('status', 'enrolled')->count();
    }

    public function getTotalCompletedStudentsAttribute()
    {
        return $this->enrollments()->where('status', 'completed')->count();
    }
}