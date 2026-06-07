<?php
// database/migrations/xxxx_add_session_to_unique_attendance_constraint.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Add session column if it doesn't exist yet
            if (!Schema::hasColumn('attendances', 'session')) {
                $table->enum('session', ['AM', 'PM'])->default('AM')->after('date');
            }

            // Drop the old unique constraint that only covers (student_id, date)
            $table->dropUnique('unique_daily_attendance');

            // Add new unique constraint that allows one AM and one PM per student per day
            $table->unique(['student_id', 'date', 'session'], 'unique_daily_attendance_session');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropUnique('unique_daily_attendance_session');
            $table->unique(['student_id', 'date'], 'unique_daily_attendance');
        });
    }
};