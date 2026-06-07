<?php
// app/Http/Controllers/Teacher/AttendanceController.php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Internship;
use App\Models\Section;
use App\Models\User;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index()
    {
        $teacherId = auth()->id();

        // All internship IDs under this teacher
        $internshipIds = Internship::where('teacher_id', $teacherId)->pluck('id');

        // Sections assigned to this teacher
        $assignedSectionIds = DB::table('subject_section')
            ->where('teacher_id', $teacherId)
            ->pluck('section_id')
            ->unique();

        $allSections = Section::whereIn('id', $assignedSectionIds)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $today = Carbon::today();

        // Today's attendance records keyed by student_id + session for quick lookup
        // e.g. "42_AM", "42_PM"
        $todayRecords = Attendance::whereIn('internship_id', $internshipIds)
            ->whereDate('date', $today)
            ->get()
            ->groupBy(fn($a) => $a->student_id . '_' . $a->session);

        // All active/pending internships for this teacher
        $internships = Internship::where('teacher_id', $teacherId)
            ->whereIn('status', ['active', 'pending'])
            ->with(['student', 'subject', 'section'])
            ->get();

        // Build section objects with student rows
        $sections = collect();
        foreach ($allSections as $section) {
            $sectionObj           = new \stdClass();
            $sectionObj->name     = $section->name;
            $sectionObj->students = collect();

            foreach ($internships as $internship) {
                if ($internship->section_id != $section->id) continue;

                $studentId = $internship->student_id;

                $entry             = new \stdClass();
                $entry->student    = $internship->student;
                $entry->internship = $internship;

                // AM record for today (null if none)
                $entry->amRecord = $todayRecords->get($studentId . '_AM')?->first();
                // PM record for today (null if none)
                $entry->pmRecord = $todayRecords->get($studentId . '_PM')?->first();

                $sectionObj->students->push($entry);
            }

            $sectionObj->students_count = $sectionObj->students->count();
            $sections->push($sectionObj);
        }

        // Summary stats
        $allTodayRecords   = Attendance::whereIn('internship_id', $internshipIds)
            ->whereDate('date', $today)->get();
        $todayAttendance   = $allTodayRecords->count();
        $studentsClockedIn = $allTodayRecords->whereNull('time_out')->count();

        // Paginated full log (all records)
        $attendances = Attendance::whereIn('internship_id', $internshipIds)
            ->with(['student', 'internship.subject', 'internship.section'])
            ->latest('date')
            ->latest('time_in')
            ->paginate(20);

        return view('teacher.attendance.index', compact(
            'sections',
            'attendances',
            'todayAttendance',
            'studentsClockedIn'
        ));
    }

    public function show(Attendance $attendance)
    {
        if ($attendance->internship->teacher_id !== auth()->id()) {
            abort(403);
        }
        return view('teacher.attendance.show', compact('attendance'));
    }

    public function byStudent($studentId)
    {
        $teacherId = auth()->id();
        $attendances = Attendance::whereHas('internship', function ($q) use ($teacherId, $studentId) {
            $q->where('teacher_id', $teacherId)->where('student_id', $studentId);
        })->with(['student', 'internship.subject'])
            ->latest('date')
            ->paginate(20);

        return view('teacher.attendance.by_student', compact('attendances'));
    }

    public function createManualAttendance(User $student)
    {
        $teacherId  = auth()->id();
        $internship = Internship::where('student_id', $student->id)
            ->where('teacher_id', $teacherId)->first();

        if (!$internship) {
            return redirect()->route('teacher.students.index')
                ->with('error', 'Student not found under your supervision.');
        }

        return view('teacher.attendance.manual', compact('student', 'internship'));
    }

    public function storeManualAttendance(Request $request, User $student)
    {
        $teacherId  = auth()->id();
        $internship = Internship::where('student_id', $student->id)
            ->where('teacher_id', $teacherId)->first();

        if (!$internship) {
            return redirect()->back()->with('error', 'Student not under your supervision.');
        }

        $validated = $request->validate([
            'date'         => 'required|date',
            'session'      => 'required|in:AM,PM',
            'time_in'      => 'required',
            'time_out'     => 'nullable',
            'hours_worked' => 'nullable|numeric|min:0',
            'status'       => 'required|in:present,late,half_day,absent',
            'notes'        => 'nullable|string',
        ]);

        $hoursWorked = $validated['hours_worked'] ?? null;
        if (!$hoursWorked && $validated['time_out']) {
            $timeIn      = Carbon::parse($validated['date'] . ' ' . $validated['time_in']);
            $timeOut     = Carbon::parse($validated['date'] . ' ' . $validated['time_out']);
            $hoursWorked = round($timeOut->diffInMinutes($timeIn) / 60, 2);
        }

        Attendance::create([
            'student_id'    => $student->id,
            'internship_id' => $internship->id,
            'subject_id'    => $internship->subject_id,
            'date'          => $validated['date'],
            'session'       => $validated['session'],
            'time_in'       => Carbon::parse($validated['date'] . ' ' . $validated['time_in']),
            'time_out'      => $validated['time_out']
                ? Carbon::parse($validated['date'] . ' ' . $validated['time_out'])
                : null,
            'hours_worked'  => $hoursWorked ?? 0,
            'status'        => $validated['status'],
            'notes'         => $validated['notes'] ?? null,
        ]);

        $internship->total_hours_rendered = $internship->attendances()->sum('hours_worked');
        $internship->save();

        return redirect()->route('teacher.students.show', $student)
            ->with('success', 'Attendance recorded successfully.');
    }

    /**
     * Time In — session (AM/PM) comes from the form hidden input.
     * Falls back to auto-detecting from current time if not provided.
     */
    public function timeIn(Request $request, User $student)
{
    $teacherId = auth()->id();
    $internship = Internship::where('student_id', $student->id)
        ->where('teacher_id', $teacherId)->first();

    if (!$internship) {
        return redirect()->back()->with('error', 'Student not under your supervision.');
    }

    $now = Carbon::now();
    $today = $now->toDateString(); // 'Y-m-d'

    // Determine session: from request or auto-detect
    $session = strtoupper($request->input('session', ''));
    if (!in_array($session, ['AM', 'PM'])) {
        $session = $now->hour < 12 ? 'AM' : 'PM';
    }

    // Critical: Use a direct where clause on the date column
    $attendance = Attendance::where('student_id', $student->id)
        ->where('internship_id', $internship->id)
        ->where('date', $today)
        ->where('session', $session)
        ->first();

    if ($attendance && $attendance->time_in !== null) {
        return redirect()->back()->with('error', "{$session} time-in already recorded for {$student->name} today.");
    }

    // Determine status (late if after 8:00 for AM, after 13:00 for PM)
    $lateHour = $session === 'AM' ? 8 : 13;
    $status = $now->hour >= $lateHour ? 'late' : 'present';

    if ($attendance) {
        // Update the existing record (e.g., if time_in was null due to a failed previous attempt)
        $attendance->update([
            'time_in' => $now,
            'status'  => $status,
        ]);
    } else {
        // Create new record
        Attendance::create([
            'student_id'    => $student->id,
            'internship_id' => $internship->id,
            'subject_id'    => $internship->subject_id,
            'date'          => $today,
            'session'       => $session,
            'time_in'       => $now,
            'status'        => $status,
        ]);
    }

    return redirect()->back()->with('success', "{$session} Time In recorded for {$student->name} at " . $now->format('h:i A'));
}

    /**
     * Time Out — finds the open record for the matching session (AM/PM).
     */
   public function timeOut(Request $request, User $student)
{
    $teacherId = auth()->id();
    $internship = Internship::where('student_id', $student->id)
        ->where('teacher_id', $teacherId)->first();

    if (!$internship) {
        return redirect()->back()->with('error', 'Student not under your supervision.');
    }

    $now = Carbon::now();
    $today = $now->toDateString();

    $session = strtoupper($request->input('session', ''));
    if (!in_array($session, ['AM', 'PM'])) {
        $session = $now->hour < 12 ? 'AM' : 'PM';
    }

    $attendance = Attendance::where('student_id', $student->id)
        ->where('internship_id', $internship->id)
        ->where('date', $today)
        ->where('session', $session)
        ->whereNull('time_out')
        ->first();

    if (!$attendance) {
        return redirect()->back()->with('error', "No open {$session} time-in found for {$student->name} today.");
    }

    $hoursWorked = round($now->diffInMinutes(Carbon::parse($attendance->time_in)) / 60, 2);

    $attendance->update([
        'time_out'     => $now,
        'hours_worked' => $hoursWorked,
    ]);

    // Update internship total hours
    $internship->total_hours_rendered = $internship->attendances()->sum('hours_worked');
    $internship->save();

    return redirect()->back()->with('success', "{$session} Time Out recorded for {$student->name} at " . $now->format('h:i A') . " ({$hoursWorked} hrs)");
}
}