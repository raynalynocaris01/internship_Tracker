// database/migrations/2024_01_01_000002_create_subjects_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g., IT401, CS301
            $table->string('name'); // e.g., Web Development Internship
            $table->text('description')->nullable();
            $table->integer('units')->default(3);
            $table->integer('required_hours')->default(500); // Required internship hours
            $table->enum('semester', ['1st', '2nd', 'Summer'])->default('1st');
            $table->year('school_year');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};