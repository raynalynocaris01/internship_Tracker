<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subject;
use App\Models\Section;
use App\Models\StudentSubjectEnrollment;
use App\Models\Attendance;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Fetch all data efficiently using a single query where possible
        $stats = [
            'totalTeachers' => User::where('role', 'teacher')->count(),
            'totalStudents' => User::where('role', 'student')->count(),
            'totalSubjects' => Subject::count(),
            'totalSections' => Section::count(),
            'totalEnrolled' => StudentSubjectEnrollment::where('status', 'enrolled')->count(),
            'totalCompleted' => StudentSubjectEnrollment::where('status', 'completed')->count(),
        ];
        
        // Today's attendance stats
        $todayAttendance = Attendance::whereDate('date', Carbon::today())->count();
        $studentsClockedIn = Attendance::whereDate('date', Carbon::today())
            ->whereNull('time_out')
            ->count();
        
        // Recent students (for your table)
        $recentStudents = User::where('role', 'student')
            ->latest()
            ->limit(5)
            ->get();
        
        // Top subjects with enrollment and completion rates
        $topSubjects = Subject::withCount('enrollments')
            ->with(['enrollments' => function($query) {
                $query->select('subject_id', DB::raw('COUNT(*) as completed_count'))
                    ->where('status', 'completed')
                    ->groupBy('subject_id');
            }])
            ->orderBy('enrollments_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function($subject) {
                return (object)[
                    'name' => $subject->code . ' - ' . $subject->name,
                    'student_count' => $subject->enrollments_count,
                    'completion_rate' => $subject->enrollments_count > 0 
                        ? round(($subject->enrollments->first()->completed_count ?? 0) / $subject->enrollments_count * 100, 2)
                        : 0
                ];
            });
        
        // Recent activities (formatted for your view)
        $activities = ActivityLog::with('user')
            ->latest()
            ->limit(10)
            ->get();
        
        $recentActivities = $activities->isNotEmpty() 
            ? $activities->map(fn($activity) => [
                'description' => $activity->description,
                'time' => $activity->created_at->diffForHumans()
            ])->toArray()
            : [
                ['description' => 'Welcome to your dashboard!', 'time' => 'Just now'],
                ['description' => 'System is ready and running', 'time' => 'Today'],
                ['description' => 'You can start adding teachers and subjects', 'time' => 'Now'],
            ];
        
        // Recent enrollments
        $recentEnrollments = StudentSubjectEnrollment::with(['student', 'subject'])
            ->latest()
            ->limit(5)
            ->get();
        
        return view('admin.dashboard', array_merge($stats, compact(
            'todayAttendance',
            'studentsClockedIn',
            'recentStudents',
            'topSubjects',
            'recentActivities',
            'recentEnrollments'
        )));
    }
}