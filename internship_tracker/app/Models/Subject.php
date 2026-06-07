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

    // Changed from enrollments() to internships()
    public function internships()
    {
        return $this->hasMany(Internship::class);
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

    // Helper Methods - Updated to use internships
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

    // Keep old names for backward compatibility (optional)
    public function getTotalEnrolledStudentsAttribute()
    {
        return $this->getTotalActiveInternshipsAttribute();
    }

    public function getTotalCompletedStudentsAttribute()
    {
        return $this->getTotalCompletedInternshipsAttribute();
    }

    // Get all students with active internships for this subject
    public function getActiveStudentsAttribute()
    {
        return $this->internships()
            ->where('status', 'active')
            ->with('student')
            ->get()
            ->pluck('student');
    }

    // Calculate overall completion rate for this subject
    public function getCompletionRateAttribute()
    {
        $total = $this->total_internships;
        if ($total == 0) return 0;
        
        $completed = $this->total_completed_internships;
        return round(($completed / $total) * 100, 2);
    }
}