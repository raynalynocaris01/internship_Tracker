<?php
// database/migrations/xxxx_fix_hours_worked_data.php
// Run once with: php artisan migrate
// This recalculates hours_worked for all attendance rows where time_out exists but hours_worked is 0 or negative.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    public function up(): void
    {
        // Find all records that have both time_in and time_out but wrong hours_worked
        $rows = DB::table('attendances')
            ->whereNotNull('time_in')
            ->whereNotNull('time_out')
            ->where(function ($q) {
                $q->where('hours_worked', '<=', 0)
                  ->orWhereNull('hours_worked');
            })
            ->get();

        foreach ($rows as $row) {
            $timeIn  = Carbon::parse($row->time_in);
            $timeOut = Carbon::parse($row->time_out);

            // ✅ Correct order: timeIn->diffInMinutes(timeOut) is always positive
            $minutes     = $timeIn->diffInMinutes($timeOut);
            $hoursWorked = round($minutes / 60, 2);

            // Guard: if somehow still 0 or negative, skip
            if ($hoursWorked <= 0) continue;

            DB::table('attendances')
                ->where('id', $row->id)
                ->update(['hours_worked' => $hoursWorked]);
        }

        // Now recalculate total_hours_rendered for all internships
        $internships = DB::table('internships')->get();
        foreach ($internships as $internship) {
            $total = DB::table('attendances')
                ->where('internship_id', $internship->id)
                ->sum('hours_worked');

            DB::table('internships')
                ->where('id', $internship->id)
                ->update(['total_hours_rendered' => $total]);
        }
    }

    public function down(): void
    {
        // No rollback — data correction only
    }
};