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

    // Changed from enrollments() to internships()
    public function internships()
    {
        return $this->hasMany(Internship::class);
    }

    // Changed from students() to use Internship model
    public function students()
    {
        return $this->hasManyThrough(
            User::class,
            Internship::class,  // Changed from StudentSubjectEnrollment
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

    // Accessors - Updated to use internships
    public function getCurrentInternshipCountAttribute()  // Renamed for clarity
    {
        return $this->internships()->where('status', 'active')->count();
    }

    // Keep old name for backward compatibility, but point to new method
    public function getCurrentEnrollmentCountAttribute()
    {
        return $this->getCurrentInternshipCountAttribute();
    }

    public function getAvailableSlotsAttribute()
    {
        return $this->max_students - $this->current_internship_count;
    }

    public function getIsFullAttribute()
    {
        return $this->current_internship_count >= $this->max_students;
    }

    // New accessor for active internships count
    public function getActiveInternshipsCountAttribute()
    {
        return $this->internships()->where('status', 'active')->count();
    }

    // New accessor for completed internships count
    public function getCompletedInternshipsCountAttribute()
    {
        return $this->internships()->where('status', 'completed')->count();
    }
}