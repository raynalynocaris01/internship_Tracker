<?php
// database/migrations/xxxx_xx_xx_add_session_to_attendances_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // 'AM' = morning session (before 12:00)
            // 'PM' = afternoon session (12:00 onwards)
            $table->enum('session', ['AM', 'PM'])->default('AM')->after('date');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('session');
        });
    }
};