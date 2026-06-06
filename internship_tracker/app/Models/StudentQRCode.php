<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentQRCode extends Model
{
    use HasFactory;

    protected $table = 'student_qrcodes';

    protected $fillable = [
        'student_id',
        'qr_code',
        'status',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
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

    // Helper Methods
    public function activate()
    {
        $this->status = 'active';
        $this->save();
    }

    public function deactivate()
    {
        $this->status = 'inactive';
        $this->save();
    }

    public function regenerate()
    {
        $this->qr_code = $this->generateUniqueCode();
        $this->save();
        return $this->qr_code;
    }

    private function generateUniqueCode()
    {
        do {
            $code = 'STU_' . strtoupper(uniqid());
        } while (self::where('qr_code', $code)->exists());
        
        return $code;
    }
}
