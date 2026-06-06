// database/migrations/2024_01_01_000005_create_student_subject_enrollments_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_subject_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['enrolled', 'dropped', 'completed'])->default('enrolled');
            $table->integer('total_hours_rendered')->default(0);
            $table->decimal('final_grade', 5, 2)->nullable();
            $table->date('enrollment_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->unique(['student_id', 'subject_id']);
            $table->index(['subject_id', 'status']);
            $table->index(['student_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_subject_enrollments');
    }
};