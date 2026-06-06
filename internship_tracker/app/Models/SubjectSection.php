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

    public function enrollments()
    {
        return $this->hasMany(StudentSubjectEnrollment::class, 'subject_id', 'subject_id')
                    ->where('section_id', $this->section_id);
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
}