<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('subject_qrcodes', function (Blueprint $table) {
            $table->enum('session', ['AM', 'PM', 'OT'])->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('subject_qrcodes', function (Blueprint $table) {
            $table->enum('session', ['AM', 'PM'])->nullable()->change();
        });
    }
};