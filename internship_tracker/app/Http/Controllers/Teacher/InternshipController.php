<?php
// app/Http/Controllers/Teacher/InternshipController.php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Internship;
use App\Models\User;
use App\Models\Subject;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InternshipController extends Controller
{
    public function index()
    {
        $teacherId = auth()->id();
        
        $internships = Internship::where('teacher_id', $teacherId)
            ->with(['student', 'subject', 'section'])
            ->latest()
            ->paginate(15);
        
        $stats = [
            'total' => Internship::where('teacher_id', $teacherId)->count(),
            'active' => Internship::where('teacher_id', $teacherId)->where('status', 'active')->count(),
            'completed' => Internship::where('teacher_id', $teacherId)->where('status', 'completed')->count(),
            'pending' => Internship::where('teacher_id', $teacherId)->where('status', 'pending')->count(),
        ];
        
        return view('teacher.internships.index', compact('internships', 'stats'));
    }
    
    public function create()
    {
        $teacherId = auth()->id();
        
        // Get students not yet assigned to an active internship
        $students = User::where('role', 'student')
            ->whereDoesntHave('internships', function($q) {
                $q->where('status', 'active');
            })
            ->get();
        
        $subjects = Subject::where('status', 'active')->get();
        
        return view('teacher.internships.create', compact('students', 'subjects'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'company_name' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'remarks' => 'nullable|string',
        ]);
        
        $validated['teacher_id'] = auth()->id();
        $validated['status'] = 'active';
        $validated['start_date'] = $validated['start_date'] ?? Carbon::today();
        
        Internship::create($validated);
        
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