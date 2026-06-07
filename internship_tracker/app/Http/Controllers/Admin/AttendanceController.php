<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Internship;
use App\Models\User;
use App\Models\Subject;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with(['student', 'internship.subject']);
        
        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }
        
        // Filter by student
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        
        // Filter by subject (through internship)
        if ($request->filled('subject_id')) {
            $query->whereHas('internship', function($q) use ($request) {
                $q->where('subject_id', $request->subject_id);
            });
        }
        
        // Filter by internship
        if ($request->filled('internship_id')) {
            $query->where('internship_id', $request->internship_id);
        }
        
        $attendances = $query->latest('date')->latest('time_in')->paginate(20);
        
        $students = User::where('role', 'student')->get();
        $subjects = Subject::where('status', 'active')->get();
        $internships = Internship::with(['student', 'subject'])->active()->get();
        
        return view('admin.attendances.index', compact('attendances', 'students', 'subjects', 'internships'));
    }

    public function show(Attendance $attendance)
    {
        $attendance->load(['student', 'internship.subject', 'internship.teacher']);
        return view('admin.attendances.show', compact('attendance'));
    }

    public function studentAttendance(User $student)
    {
        $attendances = Attendance::where('student_id', $student->id)
            ->with(['internship.subject'])
            ->latest('date')
            ->paginate(20);
            
        $totalHours = $attendances->sum('hours_worked');
        $totalDays = $attendances->count();
        $totalLate = $attendances->where('status', 'late')->count();
        
        // Get student's active internship
        $activeInternship = $student->internships()->active()->first();
        
        return view('admin.attendances.student', compact('student', 'attendances', 'totalHours', 'totalDays', 'totalLate', 'activeInternship'));
    }

    public function subjectAttendance(Subject $subject)
    {
        // Get all internships for this subject
        $internshipIds = Internship::where('subject_id', $subject->id)->pluck('id');
        
        $attendances = Attendance::whereIn('internship_id', $internshipIds)
            ->with(['student', 'internship'])
            ->latest('date')
            ->paginate(20);
            
        $totalStudents = Internship::where('subject_id', $subject->id)
            ->where('status', 'active')
            ->count();
        $totalHours = $attendances->sum('hours_worked');
        
        return view('admin.attendances.subject', compact('subject', 'attendances', 'totalStudents', 'totalHours'));
    }

    public function internshipAttendance(Internship $internship)
    {
        $attendances = Attendance::where('internship_id', $internship->id)
            ->with(['student'])
            ->latest('date')
            ->paginate(20);
            
        $totalHours = $attendances->sum('hours_worked');
        $totalDays = $attendances->count();
        $progress = $internship->progress;
        
        return view('admin.attendances.internship', compact('internship', 'attendances', 'totalHours', 'totalDays', 'progress'));
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'subject_id' => 'nullable|exists:subjects,id',
            'internship_id' => 'nullable|exists:internships,id',
        ]);
        
        $query = Attendance::with(['student', 'internship.subject']);
        
        // Date range filter
        $query->whereBetween('date', [$request->start_date, $request->end_date]);
        
        // Filter by subject (through internship)
        if ($request->filled('subject_id')) {
            $query->whereHas('internship', function($q) use ($request) {
                $q->where('subject_id', $request->subject_id);
            });
        }
        
        // Filter by specific internship
        if ($request->filled('internship_id')) {
            $query->where('internship_id', $request->internship_id);
        }
        
        $attendances = $query->get();
        
        // Group by student
        $studentSummary = $attendances->groupBy('student_id')->map(function ($items) {
            $firstItem = $items->first();
            return (object) [
                'student' => $firstItem->student,
                'internship' => $firstItem->internship,
                'total_days' => $items->count(),
                'total_hours' => $items->sum('hours_worked'),
                'present' => $items->where('status', 'present')->count(),
                'late' => $items->where('status', 'late')->count(),
                'half_day' => $items->where('status', 'half_day')->count(),
            ];
        });
        
        // Group by subject
        $subjectSummary = $attendances->groupBy(function($item) {
            return $item->internship->subject_id ?? 0;
        })->map(function ($items) {
            $firstItem = $items->first();
            return (object) [
                'subject' => $firstItem->internship->subject ?? null,
                'total_students' => $items->unique('student_id')->count(),
                'total_hours' => $items->sum('hours_worked'),
                'total_days' => $items->count(),
            ];
        })->filter(function($item) {
            return $item->subject !== null;
        });
        
        $summary = (object) [
            'total_attendances' => $attendances->count(),
            'total_hours' => $attendances->sum('hours_worked'),
            'total_students' => $attendances->unique('student_id')->count(),
            'total_present' => $attendances->where('status', 'present')->count(),
            'total_late' => $attendances->where('status', 'late')->count(),
            'total_half_day' => $attendances->where('status', 'half_day')->count(),
        ];
        
        return view('admin.reports.attendance', compact('attendances', 'studentSummary', 'subjectSummary', 'summary', 'request'));
    }
    
    // Optional: Export to Excel/CSV
    public function exportReport(Request $request)
    {
        // Similar to generateReport but returns CSV/Excel
        // Implementation depends on your export package (Laravel Excel)
    }
}