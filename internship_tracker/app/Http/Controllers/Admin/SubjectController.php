<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::withCount('internships')
            ->with('sections')  // Eager load sections for assignment display
            ->latest()
            ->paginate(15);
        return view('admin.subjects.index', compact('subjects'));
    }

    public function create()
    {
        $sections = Section::where('status', 'active')->get();
        $teachers = User::where('role', 'teacher')->get();
        return view('admin.subjects.create', compact('sections', 'teachers'));
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
            'status' => 'required|in:active,inactive',
            'assignments' => 'nullable|array',
            'assignments.*.section_id' => 'nullable|exists:sections,id',
            'assignments.*.teacher_id' => 'nullable|exists:users,id',
            'assignments.*.status' => 'nullable|in:active,inactive',
        ]);

        $subject = Subject::create($validated);
        
        $assignmentCount = 0;
        
        // Handle teacher assignments
        if ($request->has('assignments')) {
            foreach ($request->assignments as $assignment) {
                if (!empty($assignment['section_id']) && !empty($assignment['teacher_id'])) {
                    $subject->sections()->attach($assignment['section_id'], [
                        'teacher_id' => $assignment['teacher_id'],
                        'status' => $assignment['status'] ?? 'active'
                    ]);
                    $assignmentCount++;
                }
            }
        }

        $message = "Subject '{$subject->code}' created successfully.";
        if ($assignmentCount > 0) {
            $message .= " {$assignmentCount} teacher assignment(s) added.";
        }

        return redirect()->route('admin.subjects.show', $subject)
            ->with('success', $message);
    }

    public function edit(Subject $subject)
{
    $sections = Section::where('status', 'active')->get();
    $teachers = User::where('role', 'teacher')->get();
    
    // Debug: Check if subject has sections
    \Log::info('Subject ID: ' . $subject->id);
    \Log::info('Sections count: ' . $subject->sections()->count());
    
    $existingAssignments = $subject->sections()
        ->withPivot('teacher_id', 'status')
        ->get()
        ->map(function($section) {
            return [
                'section_id' => $section->id,
                'teacher_id' => $section->pivot->teacher_id,
                'status' => $section->pivot->status
            ];
        });
    
    // Debug: Check existing assignments
    \Log::info('Existing assignments: ' . json_encode($existingAssignments));
    
    return view('admin.subjects.edit', compact('subject', 'sections', 'teachers', 'existingAssignments'));
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
            'status' => 'required|in:active,inactive',
            'assignments' => 'nullable|array',
            'assignments.*.section_id' => 'required_with:assignments.*|exists:sections,id',
            'assignments.*.teacher_id' => 'required_with:assignments.*|exists:users,id',
            'assignments.*.status' => 'nullable|in:active,inactive',
        ]);

        $subject->update($validated);
        
        // Sync teacher assignments
        $syncData = [];
        if ($request->has('assignments')) {
            foreach ($request->assignments as $assignment) {
                if (!empty($assignment['section_id']) && !empty($assignment['teacher_id'])) {
                    $syncData[$assignment['section_id']] = [
                        'teacher_id' => $assignment['teacher_id'],
                        'status' => $assignment['status'] ?? 'active'
                    ];
                }
            }
        }
        
        $subject->sections()->sync($syncData);

        $message = "Subject '{$subject->code}' updated successfully.";
        if (count($syncData) > 0) {
            $message .= " " . count($syncData) . " teacher assignment(s) synced.";
        }

        return redirect()->route('admin.subjects.show', $subject)
            ->with('success', $message);
    }

    public function show(Subject $subject)
    {
        $subject->load(['sections' => function($query) {
            $query->withPivot('teacher_id', 'status', 'created_at');
        }, 'internships.student']);
        
        // Load teacher data for each section assignment
        foreach ($subject->sections as $section) {
            if ($section->pivot->teacher_id) {
                $section->assigned_teacher = User::find($section->pivot->teacher_id);
            }
        }
        
        return view('admin.subjects.show', compact('subject'));
    }

    public function destroy(Subject $subject)
    {
        // Check if subject has active internships
        if ($subject->internships()->count() > 0) {
            return redirect()->route('admin.subjects.index')
                ->with('error', 'Cannot delete subject with existing internships.');
        }
        
        // Detach sections first
        $subject->sections()->detach();
        $subject->delete();
        
        return redirect()->route('admin.subjects.index')
            ->with('success', "Subject '{$subject->code}' deleted successfully.");
    }
}