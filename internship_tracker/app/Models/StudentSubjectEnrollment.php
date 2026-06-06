<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSubjectEnrollment extends Model
{
    use HasFactory;

    protected $table = 'student_subject_enrollments';

    protected $fillable = [
        'student_id',
        'subject_id',
        'section_id',
        'teacher_id',
        'status',
        'total_hours_rendered',
        'final_grade',
        'enrollment_date',
        'completion_date',
        'remarks',
    ];

    protected $casts = [
        'total_hours_rendered' => 'integer',
        'final_grade' => 'decimal:2',
        'enrollment_date' => 'date',
        'completion_date' => 'date',
    ];

    // Status constants
    const STATUS_ENROLLED = 'enrolled';
    const STATUS_DROPPED = 'dropped';
    const STATUS_COMPLETED = 'completed';

    // Relationships
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

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

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'enrollment_id');
    }

    // Scopes
    public function scopeEnrolled($query)
    {
        return $query->where('status', self::STATUS_ENROLLED);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    // Accessors
    public function getProgressAttribute()
    {
        if ($this->subject->required_hours == 0) {
            return 0;
        }
        
        $progress = ($this->total_hours_rendered / $this->subject->required_hours) * 100;
        return min(round($progress, 2), 100);
    }

    public function getRemainingHoursAttribute()
    {
        $remaining = $this->subject->required_hours - $this->total_hours_rendered;
        return max($remaining, 0);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_ENROLLED => 'primary',
            self::STATUS_DROPPED => 'danger',
            self::STATUS_COMPLETED => 'success'
        ];
        
        return $badges[$this->status] ?? 'secondary';
    }

    // Helper Methods
    public function updateTotalHours()
    {
        $this->total_hours_rendered = $this->attendances()->sum('hours_worked');
        $this->save();
        
        // Check if completed
        if ($this->total_hours_rendered >= $this->subject->required_hours && $this->status !== self::STATUS_COMPLETED) {
            $this->status = self::STATUS_COMPLETED;
            $this->completion_date = now();
            $this->save();
        }
        
        return $this->total_hours_rendered;
    }

    public function isCompleted()
    {
        return $this->total_hours_rendered >= $this->subject->required_hours;
    }
}