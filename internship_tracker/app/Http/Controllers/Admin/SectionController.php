<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index()
    {
        $sections = Section::withCount('internships')  // Changed from 'enrollments'
            ->latest()
            ->paginate(15);
        return view('admin.sections.index', compact('sections'));
    }

    public function create()
    {
        return view('admin.sections.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:sections|max:20',
            'year_level' => 'required|integer|min:1|max:4',
            'course' => 'required|string|in:BSIT,BSCS,BSIS,BSECE',
            'max_students' => 'required|integer|min:1|max:60',
            'status' => 'required|in:active,inactive'
        ]);

        Section::create($validated);

        return redirect()->route('admin.sections.index')
            ->with('success', 'Section created successfully.');
    }

    public function show(Section $section)
    {
        $section->load(['internships.student', 'subjects']);  // Changed from 'enrollments.student'
        return view('admin.sections.show', compact('section'));
    }

    public function edit(Section $section)
    {
        return view('admin.sections.edit', compact('section'));
    }

    public function update(Request $request, Section $section)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:sections,code,' . $section->id,
            'year_level' => 'required|integer|min:1|max:4',
            'course' => 'required|string|in:BSIT,BSCS,BSIS,BSECE',
            'max_students' => 'required|integer|min:1|max:60',
            'status' => 'required|in:active,inactive'
        ]);

        $section->update($validated);

        return redirect()->route('admin.sections.index')
            ->with('success', 'Section updated successfully.');
    }

    public function destroy(Section $section)
    {
        // Check if section has any internships (students assigned)
        if ($section->internships()->count() > 0) {  // Changed from 'enrollments'
            return redirect()->route('admin.sections.index')
                ->with('error', 'Cannot delete section with assigned internships.');
        }
        
        $section->delete();
        return redirect()->route('admin.sections.index')
            ->with('success', 'Section deleted successfully.');
    }
}