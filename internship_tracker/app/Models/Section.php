<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'year_level',
        'course',
        'max_students',
        'status',
    ];

    // Relationships
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_section')
                    ->withPivot('teacher_id', 'status')
                    ->withTimestamps();
    }

    public function enrollments()
    {
        return $this->hasMany(StudentSubjectEnrollment::class);
    }

    public function students()
    {
        return $this->hasManyThrough(
            User::class,
            StudentSubjectEnrollment::class,
            'section_id',
            'id',
            'id',
            'student_id'
        );
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByCourse($query, $course)
    {
        return $query->where('course', $course);
    }

    // Accessors
    public function getCurrentEnrollmentCountAttribute()
    {
        return $this->enrollments()->where('status', 'enrolled')->count();
    }

    public function getAvailableSlotsAttribute()
    {
        return $this->max_students - $this->current_enrollment_count;
    }

    public function getIsFullAttribute()
    {
        return $this->current_enrollment_count >= $this->max_students;
    }
}