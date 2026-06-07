<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Internship extends Model
{
    use HasFactory;

    protected $table = 'internships';

    protected $fillable = [
        'student_id',
        'subject_id',
        'section_id',
        'teacher_id',
        'company_name',
        'position',
        'status',
        'total_hours_rendered',
        'final_grade',
        'start_date',
        'completion_date',
        'remarks',
    ];

    protected $casts = [
        'total_hours_rendered' => 'integer',
        'final_grade' => 'decimal:2',
        'start_date' => 'date',
        'completion_date' => 'date',
    ];

    // Status constants - MATCH your controller
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_DROPPED = 'dropped';
    const STATUS_PENDING = 'pending';

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
        return $this->hasMany(Attendance::class, 'internship_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeDropped($query)
    {
        return $query->where('status', self::STATUS_DROPPED);
    }

    // Accessors
    public function getTotalHoursAttribute()
    {
        return $this->total_hours_rendered;
    }

    public function getRequiredHoursAttribute()
    {
        return $this->subject ? $this->subject->required_hours : 0;
    }

    public function getProgressAttribute()
    {
        $requiredHours = $this->required_hours;
        if ($requiredHours == 0) {
            return 0;
        }
        
        $progress = ($this->total_hours_rendered / $requiredHours) * 100;
        return min(round($progress, 2), 100);
    }

    public function getRemainingHoursAttribute()
    {
        $remaining = $this->required_hours - $this->total_hours_rendered;
        return max($remaining, 0);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_ACTIVE => 'success',
            self::STATUS_COMPLETED => 'info',
            self::STATUS_DROPPED => 'danger',
            self::STATUS_PENDING => 'warning'
        ];
        
        return $badges[$this->status] ?? 'secondary';
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_DROPPED => 'Dropped',
            self::STATUS_PENDING => 'Pending'
        ];
        
        return $labels[$this->status] ?? ucfirst($this->status);
    }

    public function getDurationInDaysAttribute()
    {
        if (!$this->completion_date) {
            return 0;
        }
        return $this->start_date->diffInDays($this->completion_date);
    }

    // Helper Methods
    public function updateTotalHours()
    {
        $this->total_hours_rendered = $this->attendances()->sum('hours_worked');
        $this->save();
        
        // Check if completed
        if ($this->total_hours_rendered >= $this->required_hours && $this->status !== self::STATUS_COMPLETED) {
            $this->status = self::STATUS_COMPLETED;
            $this->completion_date = Carbon::now();
            $this->save();
        }
        
        return $this->total_hours_rendered;
    }

    public function isCompleted()
    {
        return $this->total_hours_rendered >= $this->required_hours;
    }

    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function markAsCompleted()
    {
        $this->status = self::STATUS_COMPLETED;
        $this->completion_date = Carbon::now();
        $this->save();
    }

    public function markAsDropped()
    {
        $this->status = self::STATUS_DROPPED;
        $this->save();
    }

    public function getTodayAttendance()
    {
        return $this->attendances()
            ->whereDate('date', Carbon::today())
            ->first();
    }

    public function getWeeklyAttendances()
    {
        return $this->attendances()
            ->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->get();
    }
}