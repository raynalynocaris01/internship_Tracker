<?php
// app/Models/SubjectQRCode.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SubjectQRCode extends Model
{
    use HasFactory;

    protected $table = 'subject_qrcodes';

    protected $fillable = [
        'subject_id',
        'section_id',
        'teacher_id',
        'qr_token',
        'session',
        'valid_date',
        'is_active',
    ];

    protected $casts = [
        'valid_date' => 'date',
        'is_active'  => 'boolean',
    ];

    // ── Relationships ─────────────────────────────────────────────

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

    // ── Helpers ───────────────────────────────────────────────────

    public function isValidToday(): bool
    {
        return $this->is_active && $this->valid_date->isToday();
    }

    public static function generateToken(): string
    {
        do {
            $token = bin2hex(random_bytes(16)); // 32-char hex
        } while (self::where('qr_token', $token)->exists());

        return $token;
    }
}