<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Internship;
use App\Models\Attendance;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $teacherId = auth()->id();
        
        // Get students under this teacher (through internships)
        $students = User::whereHas('internships', function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        })->with(['internships' => function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId)->with('subject');
        }, 'attendances'])->get();
        
        // Statistics
        $totalStudents = $students->count();
        $activeInternships = Internship::where('teacher_id', $teacherId)
            ->where('status', 'active')
            ->count();
        
        // Today's attendance
        $todayAttendance = Attendance::whereIn('student_id', $students->pluck('id'))
            ->whereDate('date', Carbon::today())
            ->count();
        
        return view('teacher.dashboard', compact('students', 'totalStudents', 'activeInternships', 'todayAttendance'));
    }
}