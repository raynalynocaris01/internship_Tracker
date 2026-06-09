<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Internship;
use App\Models\Attendance;
use App\Models\Subject;
use App\Models\Section;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $teacherId = auth()->id();

        // Do NOT eager load student.internships — that's what caused the cross-section bleed.
        // We attach the correct internship manually per student below.
        $internships = Internship::where('teacher_id', $teacherId)
            ->with(['student.attendances', 'subject', 'section'])
            ->get();

        $sectionsMap = [];

        foreach ($internships as $internship) {
            if (!$internship->section) continue;

            $sectionId = $internship->section->id;

            if (!isset($sectionsMap[$sectionId])) {
                $sectionsMap[$sectionId] = (object)[
                    'id'             => $sectionId,
                    'name'           => $internship->section->name,
                    'students'       => collect(),
                    'students_count' => 0,
                ];
            }

            // Clone so each section has an independent student object
            $student = clone $internship->student;

            // ✅ KEY FIX: store the internship as a plain property, NOT as a relation.
            // The blade must use $student->currentInternship, NOT $student->internships->first()
            $student->currentInternship = $internship;

            $sectionsMap[$sectionId]->students->push($student);
            $sectionsMap[$sectionId]->students_count = $sectionsMap[$sectionId]->students->count();
        }

        $sections = collect(array_values($sectionsMap))->sortBy('name')->values();

        $totalStudents     = $sections->sum('students_count');
        $activeInternships = $internships->where('status', 'active')->count();

        $totalHours = Attendance::whereHas('internship', function ($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        })->sum('hours_worked');

        $todayAttendance = Attendance::whereHas('internship', function ($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        })->whereDate('date', Carbon::today())->count();

        $subjectIds = DB::table('subject_section')
            ->where('teacher_id', $teacherId)
            ->pluck('subject_id')
            ->unique();

        $subjects = Subject::whereIn('id', $subjectIds)
            ->where('status', 'active')
            ->get();

        return view('teacher.dashboard', compact(
            'sections',
            'totalStudents',
            'activeInternships',
            'totalHours',
            'todayAttendance',
            'subjects'
        ));
    }
}