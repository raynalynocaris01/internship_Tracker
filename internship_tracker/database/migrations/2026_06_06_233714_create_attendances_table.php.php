<?php

// database/migrations/2024_01_01_000007_create_attendances_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('internship_id')->constrained('internships')->onDelete('cascade');  // Changed from enrollment_id
            $table->date('date');
            $table->time('time_in');
            $table->time('time_out')->nullable();
            $table->decimal('hours_worked', 5, 2)->default(0);
            $table->string('qr_code_scanned')->nullable();
            $table->enum('status', ['present', 'absent', 'late', 'half_day'])->default('present');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // One attendance per student per day (simplified)
            $table->unique(['student_id', 'date'], 'unique_daily_attendance');
            $table->index(['date', 'status']);
            $table->index(['internship_id', 'date']);  // Changed from subject_id to internship_id
            $table->index(['student_id', 'internship_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};