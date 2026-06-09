<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoTimeoutAttendance extends Command
{
    protected $signature = 'attendance:auto-timeout';
    protected $description = 'Automatically timeout AM sessions at 12:15 PM and PM sessions at 5:15 PM';

    public function handle()
    {
        $now = Carbon::now();
        $today = $now->toDateString();

        // AM session cut-off: 12:15 PM
        $amCutoff = Carbon::today()->setTime(12, 15, 0);
        // PM session cut-off: 5:15 PM
        $pmCutoff = Carbon::today()->setTime(17, 15, 0);

        $updatedCount = 0;

        // Process AM sessions
        if ($now->gte($amCutoff) && $now->lt($pmCutoff)) {
            $amAttendances = Attendance::where('session', 'AM')
                ->whereDate('date', $today)
                ->whereNull('time_out')
                ->get();

            foreach ($amAttendances as $attendance) {
                // Set time_out to the actual cutoff time (12:15) not current time
                $attendance->time_out = $amCutoff;
                $attendance->save();
                $this->calculateAttendanceHours($attendance);
                $updatedCount++;
            }
        }

        // Process PM sessions (after 5:15 PM)
        if ($now->gte($pmCutoff)) {
            $pmAttendances = Attendance::where('session', 'PM')
                ->whereDate('date', $today)
                ->whereNull('time_out')
                ->get();

            foreach ($pmAttendances as $attendance) {
                $attendance->time_out = $pmCutoff;
                $attendance->save();
                $this->calculateAttendanceHours($attendance);
                $updatedCount++;
            }
        }

        Log::info("Auto-timeout completed. Updated {$updatedCount} attendance records.");
        $this->info("Auto-timeout completed. Updated {$updatedCount} records.");
    }

    /**
     * Calculate hours worked for an attendance record (reuse logic)
     */
    private function calculateAttendanceHours($attendance)
    {
        if ($attendance->time_in && $attendance->time_out) {
            $timeIn = Carbon::parse($attendance->time_in);
            $timeOut = Carbon::parse($attendance->time_out);
            $diffInSeconds = $timeOut->diffInSeconds($timeIn);
            $hours = $diffInSeconds / 3600;

            // Subtract 1 hour for lunch if worked more than 5 hours
            if ($hours > 5) {
                $hours -= 1;
            }

            $attendance->hours_worked = round($hours, 2);
            $attendance->save();

            // Update internship total hours
            if ($attendance->internship) {
                $totalHours = $attendance->internship->attendances()->sum('hours_worked');
                $attendance->internship->total_hours_rendered = $totalHours;
                $attendance->internship->save();

                // Check completion
                if ($totalHours >= $attendance->internship->subject->required_hours) {
                    $attendance->internship->status = 'completed';
                    $attendance->internship->completion_date = Carbon::today();
                    $attendance->internship->save();
                }
            }
        }
        return $attendance->hours_worked;
    }
}