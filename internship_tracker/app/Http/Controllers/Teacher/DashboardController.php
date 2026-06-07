<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\StudentSubjectEnrollment;
use App\Models\Attendance;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $teacherId = auth()->id();
        
        // Get students under this teacher
        $enrollments = StudentSubjectEnrollment::where('teacher_id', $teacherId)
            ->with(['student', 'subject', 'section'])
            ->where('status', 'enrolled')
            ->get();
        
        $totalStudents = $enrollments->count();
        
        // Today's attendance for teacher's students
        $todayAttendance = Attendance::whereIn('student_id', $enrollments->pluck('student_id'))
            ->whereDate('date', Carbon::today())
            ->get();
            
        $studentsClockedIn = $todayAttendance->whereNull('time_out')->count();
        $completedToday = $todayAttendance->whereNotNull('time_out')->count();
        
        // Recent attendance
        $recentAttendance = Attendance::whereIn('student_id', $enrollments->pluck('student_id'))
            ->with(['student', 'subject'])
            ->latest()
            ->limit(20)
            ->get();
        
        return view('teacher.dashboard', compact(
            'enrollments', 'totalStudents', 'studentsClockedIn', 
            'completedToday', 'recentAttendance'
        ));
    }
}