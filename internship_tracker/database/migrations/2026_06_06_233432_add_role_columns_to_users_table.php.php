// database/migrations/2024_01_01_000001_add_role_columns_to_users_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'teacher', 'student'])->default('student')->after('email');
            $table->string('student_id')->nullable()->unique()->after('role');
            $table->string('teacher_id')->nullable()->unique()->after('student_id');
            $table->string('department')->nullable()->after('teacher_id');
            $table->string('course')->nullable()->after('department');
            $table->integer('year_level')->nullable()->after('course');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'student_id', 'teacher_id', 'department', 'course', 'year_level']);
        });
    }
};