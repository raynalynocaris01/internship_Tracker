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

    // ========== RELATIONSHIPS ==========
    
    /**
     * Subjects offered to this section (Many-to-Many)
     * Each assignment includes which teacher teaches it
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_section')
                    ->withPivot('teacher_id', 'status')
                    ->withTimestamps();
    }

    /**
     * Teachers assigned to teach in this section (Many-to-Many)
     * Each assignment includes which subject they teach
     */
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'subject_section', 'section_id', 'teacher_id')
                    ->withPivot('subject_id', 'status')
                    ->withTimestamps();
    }

    /**
     * Internships in this section
     */
    public function internships()
    {
        return $this->hasMany(Internship::class);
    }

    /**
     * Students in this section (through internships)
     */
    public function students()
    {
        return $this->hasManyThrough(
            User::class,
            Internship::class,
            'section_id',
            'id',
            'id',
            'student_id'
        )->distinct();
    }

    // ========== SCOPES ==========
    
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByCourse($query, $course)
    {
        return $query->where('course', $course);
    }

    // ========== ACCESSORS ==========
    
    public function getActiveInternshipsCountAttribute()
    {
        return $this->internships()->where('status', 'active')->count();
    }

    public function getTotalInternshipsCountAttribute()
    {
        return $this->internships()->count();
    }

    public function getAvailableSlotsAttribute()
    {
        return $this->max_students - $this->active_internships_count;
    }

    public function getIsFullAttribute()
    {
        return $this->active_internships_count >= $this->max_students;
    }

    public function getCompletedInternshipsCountAttribute()
    {
        return $this->internships()->where('status', 'completed')->count();
    }

    // ========== TEACHER ASSIGNMENT METHODS ==========
    
    /**
     * Get all subject assignments for this section
     * Returns collection with subject and teacher info
     */
    public function getSubjectAssignmentsAttribute()
    {
        return $this->subjects()
            ->withPivot('teacher_id', 'status')
            ->get()
            ->map(function($subject) {
                $teacher = User::find($subject->pivot->teacher_id);
                return (object)[
                    'subject' => $subject,
                    'teacher' => $teacher,
                    'status' => $subject->pivot->status
                ];
            });
    }

    /**
     * Get the teacher teaching a specific subject in this section
     */
    public function getTeacherForSubject($subjectId)
    {
        $assignment = $this->subjects()
            ->where('subject_id', $subjectId)
            ->withPivot('teacher_id')
            ->first();
        
        return $assignment ? User::find($assignment->pivot->teacher_id) : null;
    }
}