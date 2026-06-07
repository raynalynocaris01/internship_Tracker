<?php
// app/Http/Controllers/Teacher/AttendanceController.php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Internship;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $teacherId = auth()->id();
        
        // Get all internships under this teacher
        $internshipIds = Internship::where('teacher_id', $teacherId)->pluck('id');
        
        $attendances = Attendance::whereIn('internship_id', $internshipIds)
            ->with(['student', 'internship.subject'])
            ->latest('date')
            ->latest('time_in')
            ->paginate(20);
        
        // Today's attendance summary
        $todayAttendance = Attendance::whereIn('internship_id', $internshipIds)
            ->whereDate('date', Carbon::today())
            ->count();
        
        $studentsClockedIn = Attendance::whereIn('internship_id', $internshipIds)
            ->whereDate('date', Carbon::today())
            ->whereNull('time_out')
            ->count();
        
        return view('teacher.attendance.index', compact('attendances', 'todayAttendance', 'studentsClockedIn'));
    }
    
    public function show(Attendance $attendance)
    {
        if ($attendance->internship->teacher_id !== auth()->id()) {
            abort(403);
        }
        
        return view('teacher.attendance.show', compact('attendance'));
    }
    
    public function byStudent($studentId)
    {
        $teacherId = auth()->id();
        
        $attendances = Attendance::whereHas('internship', function($q) use ($teacherId, $studentId) {
            $q->where('teacher_id', $teacherId)->where('student_id', $studentId);
        })->with(['student', 'internship.subject'])
          ->latest('date')
          ->paginate(20);
        
        return view('teacher.attendance.by_student', compact('attendances'));
    }
}