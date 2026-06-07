<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subject;
use App\Models\Section;
use App\Models\Internship;  // Changed from StudentSubjectEnrollment
use App\Models\Attendance;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Fetch all data efficiently
        $stats = [
            'totalTeachers' => User::where('role', 'teacher')->count(),
            'totalStudents' => User::where('role', 'student')->count(),
            'totalSubjects' => Subject::count(),
            'totalSections' => Section::count(),
            'totalActive' => Internship::where('status', 'active')->count(),      // Changed
            'totalCompleted' => Internship::where('status', 'completed')->count(), // Changed
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
        
        // Top subjects with internship and completion rates
        $topSubjects = Subject::withCount('internships')  // Changed from 'enrollments'
            ->with(['internships' => function($query) {  // Changed from 'enrollments'
                $query->select('subject_id', DB::raw('COUNT(*) as completed_count'))
                    ->where('status', 'completed')
                    ->groupBy('subject_id');
            }])
            ->orderBy('internships_count', 'desc')  // Changed from 'enrollments_count'
            ->limit(5)
            ->get()
            ->map(function($subject) {
                return (object)[
                    'name' => $subject->code . ' - ' . $subject->name,
                    'student_count' => $subject->internships_count,  // Changed
                    'completion_rate' => $subject->internships_count > 0 
                        ? round(($subject->internships->first()->completed_count ?? 0) / $subject->internships_count * 100, 2)
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
        
        // Recent internships (changed from enrollments)
        $recentInternships = Internship::with(['student', 'subject'])  // Changed
            ->latest()
            ->limit(5)
            ->get();
        
        return view('admin.dashboard', array_merge($stats, compact(
            'todayAttendance',
            'studentsClockedIn',
            'recentStudents',
            'topSubjects',
            'recentActivities',
            'recentInternships'  // Changed variable name
        )));
    }
}