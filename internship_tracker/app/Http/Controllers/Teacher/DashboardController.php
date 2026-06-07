<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Internship;
use App\Models\Attendance;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $teacherId = auth()->id();
        
        // Get all internships under this teacher
        $internships = Internship::where('teacher_id', $teacherId)
            ->with(['student', 'subject', 'section'])
            ->get();
        
        // Group internships by section
        $groupedBySection = [];
        foreach ($internships as $internship) {
            $sectionName = $internship->section ? $internship->section->name : 'No Section';
            if (!isset($groupedBySection[$sectionName])) {
                $groupedBySection[$sectionName] = [];
            }
            $groupedBySection[$sectionName][] = $internship;
        }
        
        // Convert to collection of sections with students
        $groupedStudents = [];
        foreach ($groupedBySection as $sectionName => $sectionInternships) {
            $groupedStudents[$sectionName] = collect();
            foreach ($sectionInternships as $internship) {
                $groupedStudents[$sectionName]->push($internship->student);
            }
        }
        
        // Statistics
        $totalStudents = 0;
        foreach ($groupedStudents as $students) {
            $totalStudents += $students->count();
        }
        
        $activeInternships = $internships->where('status', 'active')->count();
        
        $totalHours = Attendance::whereHas('internship', function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        })->sum('hours_worked');
        
        $todayAttendance = Attendance::whereHas('internship', function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        })->whereDate('date', Carbon::today())
          ->count();
        
        // Get subjects assigned to this teacher
        $subjectIds = DB::table('subject_section')
            ->where('teacher_id', $teacherId)
            ->pluck('subject_id')
            ->unique();
        
        $subjects = Subject::whereIn('id', $subjectIds)->where('status', 'active')->get();
        
        return view('teacher.dashboard', compact('groupedStudents', 'totalStudents', 'activeInternships', 'totalHours', 'todayAttendance', 'subjects'));
    }
}