<?php
// app/Http/Controllers/Teacher/InternshipController.php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Internship;
use App\Models\User;
use App\Models\Section;
use App\Models\Subject;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InternshipController extends Controller
{
    public function index()
    {
        $teacherId = auth()->id();
        
        // Get all internships grouped by section - with eager loading
        $internships = Internship::where('teacher_id', $teacherId)
            ->with(['student', 'subject', 'section'])
            ->latest()
            ->get();
        
        // Group internships by section
        $groupedBySection = [];
        foreach ($internships as $internship) {
            // Make sure we get the section name correctly
            $sectionName = $internship->section ? $internship->section->name : 'No Section';
            
            if (!isset($groupedBySection[$sectionName])) {
                $groupedBySection[$sectionName] = [];
            }
            $groupedBySection[$sectionName][] = $internship;
        }
        
        // Sort sections alphabetically
        ksort($groupedBySection);
        
        // Statistics
        $stats = [
            'total' => $internships->count(),
            'active' => $internships->where('status', 'active')->count(),
            'completed' => $internships->where('status', 'completed')->count(),
            'pending' => $internships->where('status', 'pending')->count(),
        ];
        
        return view('teacher.internships.index', compact('groupedBySection', 'stats'));
    }
    
    public function create()
    {
        $teacherId = auth()->id();
        
        // Get students without active internships under this teacher
        $students = User::where('role', 'student')
            ->whereDoesntHave('internships', function($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId)->where('status', 'active');
            })->get();
        
        // Get sections assigned to this teacher (ONCE, not twice!)
        $sectionIds = DB::table('subject_section')
            ->where('teacher_id', $teacherId)
            ->pluck('section_id')
            ->unique();
        
        $sections = Section::whereIn('id', $sectionIds)->where('status', 'active')->get();
        
        // Get subjects assigned to this teacher
        $subjectIds = DB::table('subject_section')
            ->where('teacher_id', $teacherId)
            ->pluck('subject_id')
            ->unique();
        
        $subjects = Subject::whereIn('id', $subjectIds)->where('status', 'active')->get();
        
        return view('teacher.internships.create', compact('students', 'sections', 'subjects'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'required|exists:subjects,id',
            'company_name' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'remarks' => 'nullable|string',
        ]);

        // Check if student already has an active internship
        $existing = Internship::where('student_id', $validated['student_id'])
            ->where('status', 'active')
            ->first();
            
        if ($existing) {
            return redirect()->back()
                ->with('error', 'Student already has an active internship.')
                ->withInput();
        }

        Internship::create([
            'student_id' => $validated['student_id'],
            'section_id' => $validated['section_id'],
            'subject_id' => $validated['subject_id'],
            'teacher_id' => auth()->id(),
            'company_name' => $validated['company_name'],
            'position' => $validated['position'],
            'start_date' => $validated['start_date'],
            'status' => 'active',
            'remarks' => $validated['remarks'],
        ]);

        return redirect()->route('teacher.internships.index')
            ->with('success', 'Internship assigned successfully.');
    }
    
    public function show(Internship $internship)
    {
        // Verify this internship belongs to the teacher
        if ($internship->teacher_id !== auth()->id()) {
            abort(403);
        }
        
        $internship->load(['student', 'subject', 'section', 'attendances']);
        $totalHours = $internship->attendances->sum('hours_worked');
        $progress = $internship->progress;
        
        return view('teacher.internships.show', compact('internship', 'totalHours', 'progress'));
    }
    
    public function edit(Internship $internship)
    {
        if ($internship->teacher_id !== auth()->id()) {
            abort(403);
        }
        
        return view('teacher.internships.edit', compact('internship'));
    }
    
    public function update(Request $request, Internship $internship)
    {
        if ($internship->teacher_id !== auth()->id()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'status' => 'required|in:active,completed,pending,dropped',
            'remarks' => 'nullable|string',
        ]);
        
        if ($validated['status'] === 'completed' && $internship->status !== 'completed') {
            $validated['completion_date'] = Carbon::today();
        }
        
        $internship->update($validated);
        
        return redirect()->route('teacher.internships.index')
            ->with('success', 'Internship updated successfully.');
    }
    
    public function destroy(Internship $internship)
    {
        if ($internship->teacher_id !== auth()->id()) {
            abort(403);
        }
        
        if ($internship->attendances()->count() > 0) {
            return redirect()->route('teacher.internships.index')
                ->with('error', 'Cannot delete internship with attendance records.');
        }
        
        $internship->delete();
        
        return redirect()->route('teacher.internships.index')
            ->with('success', 'Internship deleted successfully.');
    }
}