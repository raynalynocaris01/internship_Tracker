<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::withCount('internships')  // Changed from 'enrollments'
            ->latest()
            ->paginate(15);
        return view('admin.subjects.index', compact('subjects'));
    }

    public function create()
    {
        return view('admin.subjects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:subjects|max:20',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'units' => 'required|integer|min:1|max:9',
            'required_hours' => 'required|integer|min:100|max:1000',
            'semester' => 'required|in:1st,2nd,Summer',
            'school_year' => 'required|integer|min:2000|max:2100',
            'status' => 'required|in:active,inactive'
        ]);

        Subject::create($validated);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject created successfully.');
    }

    public function show(Subject $subject)
    {
        $subject->load(['internships.student', 'sections']);  // Changed from 'enrollments.student'
        return view('admin.subjects.show', compact('subject'));
    }

    public function edit(Subject $subject)
    {
        return view('admin.subjects.edit', compact('subject'));
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:subjects,code,' . $subject->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'units' => 'required|integer|min:1|max:9',
            'required_hours' => 'required|integer|min:100|max:1000',
            'semester' => 'required|in:1st,2nd,Summer',
            'school_year' => 'required|integer|min:2000|max:2100',
            'status' => 'required|in:active,inactive'
        ]);

        $subject->update($validated);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject updated successfully.');
    }

    public function destroy(Subject $subject)
    {
        // Check if subject has active internships
        if ($subject->internships()->count() > 0) {  // Changed from 'enrollments'
            return redirect()->route('admin.subjects.index')
                ->with('error', 'Cannot delete subject with existing internships.');
        }
        
        $subject->delete();
        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject deleted successfully.');
    }

    // Optional: Get statistics for a subject
    public function statistics(Subject $subject)
    {
        $stats = [
            'total_internships' => $subject->internships()->count(),
            'active_internships' => $subject->internships()->where('status', 'active')->count(),
            'completed_internships' => $subject->internships()->where('status', 'completed')->count(),
            'total_hours_rendered' => $subject->internships()->with('attendances')->get()
                ->sum(function($internship) {
                    return $internship->attendances->sum('hours_worked');
                }),
            'total_students' => $subject->internships()->distinct('student_id')->count('student_id'),
        ];

        return response()->json($stats);
    }
}