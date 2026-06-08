<?php
// database/migrations/xxxx_create_subject_qrcodes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subject_qrcodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('section_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->string('qr_token', 64)->unique();   // random token embedded in QR
            $table->enum('session', ['AM', 'PM'])->default('AM');
            $table->date('valid_date');                  // only valid on this date
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // One active QR per subject+section+session+date
            $table->unique(['subject_id', 'section_id', 'session', 'valid_date'], 'unique_subject_qr');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_qrcodes');
    }
};