<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentQRCode;
use App\Models\Internship;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class StudentController extends Controller
{
    public function index()
    {
        $teacherId = auth()->id();

        // Get all sections assigned to this teacher
        $assignedSectionIds = DB::table('subject_section')
            ->where('teacher_id', $teacherId)
            ->pluck('section_id')
            ->unique();

        // Get all sections with their details
        $allSections = Section::whereIn('id', $assignedSectionIds)
            ->where('status', 'active')
            ->get();

        // Get all internships with students — eager load attendances to avoid N+1
        $internships = Internship::where('teacher_id', $teacherId)
            ->with(['student.attendances', 'subject', 'section']) // ✅ FIX: added student.attendances
            ->get();

        // Build sections with their students
        $sections = collect();

        foreach ($allSections as $section) {
            $sectionObj = new \stdClass();
            $sectionObj->name = $section->name;
            $sectionObj->students = collect();

            // Add students that belong strictly to this section
            foreach ($internships as $internship) {
                if ($internship->section_id == $section->id) {
                    $student = clone $internship->student; // ✅ FIX: clone to prevent shared object reference
                    $student->internship = $internship;
                    $sectionObj->students->push($student);
                }
            }

            $sectionObj->students_count = $sectionObj->students->count();
            $sections->push($sectionObj);
        }

        $sections = $sections->sortBy('name')->values();

        // Get subjects assigned to this teacher
        $subjectIds = DB::table('subject_section')
            ->where('teacher_id', $teacherId)
            ->pluck('subject_id')
            ->unique();

        $subjects = Subject::whereIn('id', $subjectIds)->where('status', 'active')->get();

        return view('teacher.students.index', compact('sections', 'subjects'));
    }

    public function create()
    {
        $teacherId = auth()->id();

        // Get sections assigned to this teacher from subject_section table
        $sectionIds = DB::table('subject_section')
            ->where('teacher_id', $teacherId)
            ->pluck('section_id')
            ->unique();

        $sections = Section::whereIn('id', $sectionIds)->where('status', 'active')->get();

        // Get subjects assigned to this teacher from subject_section table
        $subjectIds = DB::table('subject_section')
            ->where('teacher_id', $teacherId)
            ->pluck('subject_id')
            ->unique();

        $subjects = Subject::whereIn('id', $subjectIds)->where('status', 'active')->get();

        return view('teacher.students.create', compact('sections', 'subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users',
            'student_id' => 'required|string|unique:users',
            'course' => 'required|string|max:255',
            'year_level' => 'required|integer|min:1|max:4',
            'password'   => 'required|string|min:8|confirmed',
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'required|exists:subjects,id',
            'start_date' => 'nullable|date',
        ]);

        // Create student account
        $user = User::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'role'       => 'student',
            'student_id' => $validated['student_id'],
            'course'     => $validated['course'],
            'year_level' => $validated['year_level'],
            'department' => 'Information Technology',
        ]);

        // Generate QR code
        StudentQRCode::create([
            'student_id' => $user->id,
            'qr_code'    => $this->generateUniqueQRCode(),
            'status'     => 'active',
        ]);

        // Create internship assignment
        Internship::create([
            'student_id' => $user->id,
            'subject_id' => $validated['subject_id'],
            'section_id' => $validated['section_id'],
            'teacher_id' => auth()->id(),
            'start_date' => $validated['start_date'] ?? Carbon::today(),
            'status'     => 'active',
        ]);

        return redirect()->route('teacher.students.index')
            ->with('success', 'Student added and internship assigned successfully!');
    }

    public function show(User $student)
    {
        $teacherId = auth()->id();

        $internship = $student->internships()
            ->where('teacher_id', $teacherId)
            ->with('subject')
            ->first();

        if (!$internship) {
            return redirect()->route('teacher.students.index')
                ->with('error', 'Student not found under your supervision.');
        }

        $attendances = Attendance::where('student_id', $student->id)
            ->with('internship.subject')
            ->latest('date')
            ->paginate(20);

        $totalHours = Attendance::where('student_id', $student->id)->sum('hours_worked');
        $totalDays  = Attendance::where('student_id', $student->id)->count();
        $progress   = $internship->progress;

        return view('teacher.students.show', compact('student', 'internship', 'attendances', 'totalHours', 'totalDays', 'progress'));
    }

    public function bulkAssign(Request $request)
    {
        $request->validate([
            'student_ids'   => 'required|array|min:1',
            'student_ids.*' => 'exists:users,id',
            'subject_id'    => 'required|exists:subjects,id',
            'start_date'    => 'nullable|date',
        ]);

        $teacherId     = auth()->id();
        $assignedCount = 0;
        $skippedCount  = 0;

        foreach ($request->student_ids as $studentId) {
            // Check if student already has an active internship
            $existing = Internship::where('student_id', $studentId)
                ->where('status', 'active')
                ->first();

            if ($existing) {
                $skippedCount++;
                continue;
            }

            Internship::create([
                'student_id' => $studentId,
                'subject_id' => $request->subject_id,
                'teacher_id' => $teacherId,
                'start_date' => $request->start_date ?? Carbon::today(),
                'status'     => 'active',
            ]);

            $assignedCount++;
        }

        $message = "Assigned {$assignedCount} student(s) to internship.";
        if ($skippedCount > 0) {
            $message .= " Skipped {$skippedCount} student(s) who already have internships.";
        }

        return redirect()->route('teacher.students.index')
            ->with('success', $message);
    }

    public function destroy(User $student)
    {
        $teacherId = auth()->id();

        // Verify this student belongs to this teacher
        $internship = Internship::where('student_id', $student->id)
            ->where('teacher_id', $teacherId)
            ->first();

        if (!$internship) {
            return redirect()->route('teacher.students.index')
                ->with('error', 'You are not authorized to delete this student.');
        }

        // Delete attendance records first
        Attendance::where('student_id', $student->id)->delete();

        // Delete QR code
        StudentQRCode::where('student_id', $student->id)->delete();

        // Delete internship
        $internship->delete();

        // Delete student user
        $student->delete();

        return redirect()->route('teacher.students.index')
            ->with('success', 'Student deleted successfully.');
    }

    private function generateUniqueQRCode()
    {
        do {
            $code = 'STU_' . strtoupper(uniqid());
        } while (StudentQRCode::where('qr_code', $code)->exists());

        return $code;
    }
}