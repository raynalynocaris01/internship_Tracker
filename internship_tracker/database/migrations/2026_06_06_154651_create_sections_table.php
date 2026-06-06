// database/migrations/2024_01_01_000003_create_sections_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., BSIT-3A, BSCS-3B
            $table->string('code')->unique();
            $table->integer('year_level'); // 1st year, 2nd year, 3rd year, 4th year
            $table->string('course'); // BSIT, BSCS, BSIS, BSECE
            $table->integer('max_students')->default(40);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};