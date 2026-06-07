<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Subject;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with(['student', 'subject']);
        
        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }
        
        // Filter by student
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        
        // Filter by subject
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        
        $attendances = $query->latest('date')->latest('time_in')->paginate(20);
        
        $students = User::where('role', 'student')->get();
        $subjects = Subject::where('status', 'active')->get();
        
        return view('admin.attendances.index', compact('attendances', 'students', 'subjects'));
    }

    public function show(Attendance $attendance)
    {
        return view('admin.attendances.show', compact('attendance'));
    }

    public function studentAttendance(User $student)
    {
        $attendances = Attendance::where('student_id', $student->id)
            ->with('subject')
            ->latest('date')
            ->paginate(20);
            
        $totalHours = $attendances->sum('hours_worked');
        $totalDays = $attendances->count();
        $totalLate = $attendances->where('status', 'late')->count();
        
        return view('admin.attendances.student', compact('student', 'attendances', 'totalHours', 'totalDays', 'totalLate'));
    }

    public function subjectAttendance(Subject $subject)
    {
        $attendances = Attendance::where('subject_id', $subject->id)
            ->with('student')
            ->latest('date')
            ->paginate(20);
            
        $totalStudents = $attendances->groupBy('student_id')->count();
        $totalHours = $attendances->sum('hours_worked');
        
        return view('admin.attendances.subject', compact('subject', 'attendances', 'totalStudents', 'totalHours'));
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'subject_id' => 'nullable|exists:subjects,id',
        ]);
        
        $query = Attendance::with(['student', 'subject'])
            ->whereBetween('date', [$request->start_date, $request->end_date]);
            
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        
        $attendances = $query->get();
        
        // Group by student
        $studentSummary = $attendances->groupBy('student_id')->map(function ($items) {
            return [
                'student' => $items->first()->student,
                'total_days' => $items->count(),
                'total_hours' => $items->sum('hours_worked'),
                'present' => $items->where('status', 'present')->count(),
                'late' => $items->where('status', 'late')->count(),
            ];
        });
        
        return view('admin.reports.attendance', compact('attendances', 'studentSummary', 'request'));
    }
}