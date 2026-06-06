<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Get statistics
        $totalTeachers = User::where('role', 'teacher')->count();
        $totalStudents = User::where('role', 'student')->count();
        $totalSubjects = 0; // Set to 0 for now until you create subjects table
        $totalSections = 0; // Set to 0 for now until you create sections table
        
        // Get recent students
        $recentStudents = User::where('role', 'student')
            ->latest()
            ->take(5)
            ->get();
        
        // Sample data for top subjects (you can replace with actual data)
        $topSubjects = [
            ['name' => 'Web Development', 'student_count' => 45, 'completion_rate' => 78],
            ['name' => 'Database Management', 'student_count' => 38, 'completion_rate' => 65],
            ['name' => 'Software Engineering', 'student_count' => 32, 'completion_rate' => 82],
            ['name' => 'Mobile App Development', 'student_count' => 28, 'completion_rate' => 45],
        ];
        
        // Sample recent activities
        $recentActivities = [
            ['description' => 'New student registered: John Doe', 'time' => '5 minutes ago'],
            ['description' => 'Teacher account created: Maria Santos', 'time' => '1 hour ago'],
            ['description' => 'Attendance recorded for 15 students', 'time' => '3 hours ago'],
            ['description' => 'System backup completed', 'time' => 'Yesterday'],
        ];
        
        return view('admin.dashboard', compact(
            'totalTeachers',
            'totalStudents',
            'totalSubjects',
            'totalSections',
            'recentStudents',
            'topSubjects',
            'recentActivities'
        ));
    }
}