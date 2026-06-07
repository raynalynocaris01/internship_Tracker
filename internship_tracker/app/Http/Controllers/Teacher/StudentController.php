<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $teacherId = auth()->id();
        
        $students = User::whereHas('studentEnrollments', function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId)->where('status', 'enrolled');
        })->with(['studentEnrollments' => function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId)->with('subject');
        }])->get();
        
        return view('teacher.students.index', compact('students'));
    }
    
    public function show(User $student)
    {
        $teacherId = auth()->id();
        
        // Verify student is under this teacher
        $enrollment = $student->studentEnrollments()
            ->where('teacher_id', $teacherId)
            ->where('status', 'enrolled')
            ->first();
            
        if (!$enrollment) {
            return redirect()->route('teacher.students.index')
                ->with('error', 'Student not found under your supervision.');
        }
        
        $attendances = Attendance::where('student_id', $student->id)
            ->with('subject')
            ->latest('date')
            ->paginate(20);
            
        $totalHours = $attendances->sum('hours_worked');
        $totalDays = $attendances->count();
        $progress = $enrollment->progress;
        
        return view('teacher.students.show', compact('student', 'enrollment', 'attendances', 'totalHours', 'totalDays', 'progress'));
    }
}