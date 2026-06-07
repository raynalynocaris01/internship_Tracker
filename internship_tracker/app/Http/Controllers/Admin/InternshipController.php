<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subject;
use App\Models\Section;
use App\Models\Internship;  // Changed from StudentSubjectEnrollment
use App\Models\StudentQRCode;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InternshipController extends Controller
{
    public function index()
    {
        $internships = Internship::with(['student', 'subject', 'section', 'teacher'])
            ->latest()
            ->paginate(20);
        
        return view('admin.internships.index', compact('internships'));
    }

    public function create()
    {
        // Get students without active internships
        $students = User::where('role', 'student')
            ->whereDoesntHave('internships', function($q) {
                $q->where('status', 'active');
            })->get();
        
        $subjects = Subject::where('status', 'active')->get();
        $sections = Section::where('status', 'active')->get();
        $teachers = User::where('role', 'teacher')->get();
        
        return view('admin.internships.create', compact('students', 'subjects', 'sections', 'teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'section_id' => 'nullable|exists:sections,id',
            'teacher_id' => 'nullable|exists:users,id',
            'start_date' => 'nullable|date',  // Changed from enrollment_date
        ]);

        // Check if student already has an active internship for this subject
        $existing = Internship::where('student_id', $validated['student_id'])
            ->where('subject_id', $validated['subject_id'])
            ->where('status', 'active')
            ->first();
            
        if ($existing) {
            return redirect()->back()
                ->with('error', 'Student already has an active internship for this subject.')
                ->withInput();
        }

        $validated['status'] = 'active';
        $validated['start_date'] = $validated['start_date'] ?? Carbon::today();
        
        $internship = Internship::create($validated);
        
        // Generate QR code for student if not exists
        if (!$internship->student->qrCode) {
            StudentQRCode::create([
                'student_id' => $internship->student_id,
                'qr_code' => 'STU_' . strtoupper(uniqid()),
                'status' => 'active'
            ]);
        }

        return redirect()->route('admin.internships.index')
            ->with('success', 'Internship assigned successfully.');
    }

    public function show(Internship $internship)
    {
        $internship->load(['student', 'subject', 'section', 'teacher', 'attendances']);
        $totalHours = $internship->attendances()->sum('hours_worked');
        $progress = $internship->progress;
        
        return view('admin.internships.show', compact('internship', 'totalHours', 'progress'));
    }

    public function edit(Internship $internship)
    {
        $teachers = User::where('role', 'teacher')->get();
        $sections = Section::where('status', 'active')->get();
        
        return view('admin.internships.edit', compact('internship', 'teachers', 'sections'));
    }

    public function update(Request $request, Internship $internship)
    {
        $validated = $request->validate([
            'teacher_id' => 'nullable|exists:users,id',
            'section_id' => 'nullable|exists:sections,id',
            'status' => 'required|in:active,completed,dropped',  // Changed status values
            'remarks' => 'nullable|string',
        ]);

        if ($validated['status'] === 'completed' && $internship->status !== 'completed') {
            $validated['completion_date'] = Carbon::today();
        }

        $internship->update($validated);

        return redirect()->route('admin.internships.index')
            ->with('success', 'Internship updated successfully.');
    }

    public function destroy(Internship $internship)
    {
        // Prevent deletion if internship has attendance records
        if ($internship->attendances()->count() > 0) {
            return redirect()->route('admin.internships.index')
                ->with('error', 'Cannot delete internship with existing attendance records.');
        }
        
        $internship->delete();
        return redirect()->route('admin.internships.index')
            ->with('success', 'Internship deleted successfully.');
    }
}