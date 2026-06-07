<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectSection extends Model
{
    use HasFactory;

    protected $table = 'subject_section';

    protected $fillable = [
        'subject_id',
        'section_id',
        'teacher_id',
        'status',
    ];

    // Relationships
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // Changed from enrollments() to internships()
    public function internships()
    {
        return $this->hasMany(Internship::class, 'subject_id', 'subject_id')
                    ->where('section_id', $this->section_id);
    }

    // Keep old method for backward compatibility (optional)
    public function enrollments()
    {
        return $this->internships();
    }

    // Get active internships count
    public function getActiveInternshipsCountAttribute()
    {
        return $this->internships()->where('status', 'active')->count();
    }

    // Get total internships count
    public function getTotalInternshipsCountAttribute()
    {
        return $this->internships()->count();
    }

    // Get completed internships count
    public function getCompletedInternshipsCountAttribute()
    {
        return $this->internships()->where('status', 'completed')->count();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        return $this->status === 'active' ? 'success' : 'danger';
    }

    // Get the teacher name with ID
    public function getTeacherNameAttribute()
    {
        return $this->teacher ? $this->teacher->name : 'Not Assigned';
    }

    // Get full display name for the subject-section pair
    public function getDisplayNameAttribute()
    {
        return $this->subject->code . ' - ' . $this->section->name;
    }
}