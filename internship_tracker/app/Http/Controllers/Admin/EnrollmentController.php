<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subject;
use App\Models\Section;
use App\Models\StudentSubjectEnrollment;
use App\Models\StudentQRCode;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EnrollmentController extends Controller
{
    public function index()
    {
        $enrollments = StudentSubjectEnrollment::with(['student', 'subject', 'section', 'teacher'])
            ->latest()
            ->paginate(20);
        
        return view('admin.enrollments.index', compact('enrollments'));
    }

    public function create()
    {
        $students = User::where('role', 'student')->whereDoesntHave('studentEnrollments', function($q) {
            $q->where('status', 'enrolled');
        })->get();
        
        $subjects = Subject::where('status', 'active')->get();
        $sections = Section::where('status', 'active')->get();
        $teachers = User::where('role', 'teacher')->get();
        
        return view('admin.enrollments.create', compact('students', 'subjects', 'sections', 'teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'section_id' => 'nullable|exists:sections,id',
            'teacher_id' => 'nullable|exists:users,id',
            'enrollment_date' => 'nullable|date',
        ]);

        // Check if student already enrolled in this subject
        $existing = StudentSubjectEnrollment::where('student_id', $validated['student_id'])
            ->where('subject_id', $validated['subject_id'])
            ->where('status', 'enrolled')
            ->first();
            
        if ($existing) {
            return redirect()->back()
                ->with('error', 'Student is already enrolled in this subject.')
                ->withInput();
        }

        $validated['status'] = 'enrolled';
        $validated['enrollment_date'] = $validated['enrollment_date'] ?? Carbon::today();
        
        $enrollment = StudentSubjectEnrollment::create($validated);
        
        // Generate QR code for student if not exists
        if (!$enrollment->student->qrCode) {
            StudentQRCode::create([
                'student_id' => $enrollment->student_id,
                'qr_code' => 'STU_' . strtoupper(uniqid()),
                'status' => 'active'
            ]);
        }

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Student enrolled successfully.');
    }

    public function show(StudentSubjectEnrollment $enrollment)
    {
        $enrollment->load(['student', 'subject', 'section', 'teacher', 'attendances']);
        $totalHours = $enrollment->attendances()->sum('hours_worked');
        $progress = $enrollment->progress;
        
        return view('admin.enrollments.show', compact('enrollment', 'totalHours', 'progress'));
    }

    public function edit(StudentSubjectEnrollment $enrollment)
    {
        $teachers = User::where('role', 'teacher')->get();
        $sections = Section::where('status', 'active')->get();
        
        return view('admin.enrollments.edit', compact('enrollment', 'teachers', 'sections'));
    }

    public function update(Request $request, StudentSubjectEnrollment $enrollment)
    {
        $validated = $request->validate([
            'teacher_id' => 'nullable|exists:users,id',
            'section_id' => 'nullable|exists:sections,id',
            'status' => 'required|in:enrolled,dropped,completed',
            'remarks' => 'nullable|string',
        ]);

        if ($validated['status'] === 'completed' && $enrollment->status !== 'completed') {
            $validated['completion_date'] = Carbon::today();
        }

        $enrollment->update($validated);

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Enrollment updated successfully.');
    }

    public function destroy(StudentSubjectEnrollment $enrollment)
    {
        $enrollment->delete();
        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Enrollment deleted successfully.');
    }
}