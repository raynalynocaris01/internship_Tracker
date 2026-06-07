<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendances';

    protected $fillable = [
        'student_id',
        'subject_id',
        'internship_id',  // Changed from 'enrollment_id'
        'date',
        'time_in',
        'time_out',
        'hours_worked',
        'qr_code_scanned',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'time_in' => 'datetime',
        'time_out' => 'datetime',
        'hours_worked' => 'decimal:2',
    ];

    // Status constants
    const STATUS_PRESENT = 'present';
    const STATUS_ABSENT = 'absent';
    const STATUS_LATE = 'late';
    const STATUS_HALF_DAY = 'half_day';

    // Relationships
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    // Changed from enrollment() to internship()
    public function internship()
    {
        return $this->belongsTo(Internship::class, 'internship_id');
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->where('date', Carbon::today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date', Carbon::now()->month)
                     ->whereYear('date', Carbon::now()->year);
    }

    public function scopePresent($query)
    {
        return $query->where('status', self::STATUS_PRESENT);
    }

    public function scopeLate($query)
    {
        return $query->where('status', self::STATUS_LATE);
    }

    // Accessors
    public function getTimeInFormattedAttribute()
    {
        return $this->time_in ? Carbon::parse($this->time_in)->format('h:i A') : null;
    }

    public function getTimeOutFormattedAttribute()
    {
        return $this->time_out ? Carbon::parse($this->time_out)->format('h:i A') : null;
    }

    public function getDateFormattedAttribute()
    {
        return $this->date ? Carbon::parse($this->date)->format('F d, Y') : null;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_PRESENT => 'success',
            self::STATUS_ABSENT => 'danger',
            self::STATUS_LATE => 'warning',
            self::STATUS_HALF_DAY => 'info'
        ];
        
        return $badges[$this->status] ?? 'secondary';
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            self::STATUS_PRESENT => 'Present',
            self::STATUS_ABSENT => 'Absent',
            self::STATUS_LATE => 'Late',
            self::STATUS_HALF_DAY => 'Half Day'
        ];
        
        return $labels[$this->status] ?? ucfirst($this->status);
    }

    // Helper Methods
    public function calculateHours()
    {
        if ($this->time_in && $this->time_out) {
            $timeIn = Carbon::parse($this->time_in);
            $timeOut = Carbon::parse($this->time_out);
            $diffInSeconds = $timeOut->diffInSeconds($timeIn);
            
            // Calculate hours
            $hours = $diffInSeconds / 3600;
            
            // Subtract 1 hour for lunch if worked more than 5 hours
            if ($hours > 5) {
                $hours -= 1;
            }
            
            $this->hours_worked = round($hours, 2);
            $this->save();
            
            // Update internship total hours (changed from enrollment)
            if ($this->internship) {
                $this->internship->updateTotalHours();
            }
        }
        
        return $this->hours_worked;
    }

    public function isTimedIn()
    {
        return $this->time_in && !$this->time_out;
    }

    public function isComplete()
    {
        return $this->time_in && $this->time_out;
    }

    public function timeOutNow()
    {
        $this->time_out = Carbon::now();
        $this->save();
        $this->calculateHours();
        $this->updateStatus();
        
        return $this;
    }

    public function isLate()
    {
        if (!$this->time_in) {
            return false;
        }
        
        $timeIn = Carbon::parse($this->time_in);
        // Get late cutoff time (default 8:30 AM)
        $cutoffTime = Carbon::parse($this->date->format('Y-m-d') . ' 08:30:00');
        return $timeIn->gt($cutoffTime);
    }

    public function updateStatus()
    {
        if ($this->isLate()) {
            $this->status = self::STATUS_LATE;
        } elseif ($this->hours_worked < 4) {
            $this->status = self::STATUS_HALF_DAY;
        } else {
            $this->status = self::STATUS_PRESENT;
        }
        $this->save();
    }

    // Additional helper: Get the subject through internship
    public function getSubjectThroughInternship()
    {
        return $this->internship ? $this->internship->subject : null;
    }
}