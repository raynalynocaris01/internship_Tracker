<?php

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

        $internshipIds = Internship::where('teacher_id', $teacherId)->pluck('id');

        $assignedSectionIds = DB::table('subject_section')
            ->where('teacher_id', $teacherId)
            ->pluck('section_id')
            ->unique();

        $allSections = Section::whereIn('id', $assignedSectionIds)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $today = Carbon::today();

        // Fetch all today's attendance records (AM, PM, OT)
        $todayRecords = Attendance::whereIn('internship_id', $internshipIds)
            ->whereDate('date', $today)
            ->get()
            ->groupBy(fn($a) => $a->student_id . '_' . ($a->session ?? 'AM'));

        $internships = Internship::where('teacher_id', $teacherId)
            ->whereIn('status', ['active', 'pending'])
            ->with(['student', 'subject', 'section'])
            ->get();

        $now = Carbon::now();
        $amCutoff = Carbon::today()->setTime(12, 00);
        $pmCutoff = Carbon::today()->setTime(17, 00);

        // Auto‑timeout AM sessions that are still open
        $openAM = Attendance::whereIn('internship_id', $internshipIds)
            ->whereDate('date', $today)
            ->where('session', 'AM')
            ->whereNull('time_out')
            ->get();

        foreach ($openAM as $attendance) {
            if ($now->gte($amCutoff)) {
                $attendance->time_out = $amCutoff;
                $attendance->save();
                $attendance->calculateHours(); // if you have this method, or recalc manually
                // Update internship totals
                $this->recalcInternshipHours($attendance->internship);
            }
        }

        // Auto‑timeout PM sessions that are still open
        $openPM = Attendance::whereIn('internship_id', $internshipIds)
            ->whereDate('date', $today)
            ->where('session', 'PM')
            ->whereNull('time_out')
            ->get();

        foreach ($openPM as $attendance) {
            if ($now->gte($pmCutoff)) {
                $attendance->time_out = $pmCutoff;
                $attendance->save();
                $attendance->calculateHours();
                $this->recalcInternshipHours($attendance->internship);
            }
        }

        // After this, re‑fetch $todayRecords to include the newly closed records
        $todayRecords = Attendance::whereIn('internship_id', $internshipIds)
            ->whereDate('date', $today)
            ->get()
            ->groupBy(fn($a) => $a->student_id . '_' . ($a->session ?? 'AM'));

        $sections = collect();
        foreach ($allSections as $section) {
            $sectionObj           = new \stdClass();
            $sectionObj->name     = $section->name;
            $sectionObj->students = collect();

            foreach ($internships as $internship) {
                if ($internship->section_id != $section->id) continue;

                $studentId         = $internship->student_id;
                $entry             = new \stdClass();
                $entry->student    = $internship->student;
                $entry->internship = $internship;
                $entry->amRecord   = $todayRecords->get($studentId . '_AM')?->first();
                $entry->pmRecord   = $todayRecords->get($studentId . '_PM')?->first();
                $entry->otRecord   = $todayRecords->get($studentId . '_OT')?->first();  // OT record

                $sectionObj->students->push($entry);
            }

            $sectionObj->students_count = $sectionObj->students->count();
            $sections->push($sectionObj);
        }

        $allTodayRecords   = Attendance::whereIn('internship_id', $internshipIds)
            ->whereDate('date', $today)->get();
        $todayAttendance   = $allTodayRecords->count();
        $studentsClockedIn = $allTodayRecords->whereNull('time_out')->count();

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
        $teacherId   = auth()->id();
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
            'session'      => 'required|in:AM,PM,OT',
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
            $hoursWorked = round(abs($timeIn->diffInMinutes($timeOut)) / 60, 2);
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

        $this->recalcInternshipHours($internship);

        return redirect()->route('teacher.students.show', $student)
            ->with('success', 'Attendance recorded successfully.');
    }

    public function timeIn(Request $request, User $student)
{
    $teacherId  = auth()->id();
    $internship = Internship::where('student_id', $student->id)
        ->where('teacher_id', $teacherId)->first();

    if (!$internship) {
        return redirect()->back()->with('error', 'Student not under your supervision.');
    }

    $now     = Carbon::now();
    $today   = $now->toDateString();
    $session = strtoupper($request->input('session', ''));

    if (!in_array($session, ['AM', 'PM', 'OT'])) {
        // Auto‑detect only if not provided
        if ($now->hour < 12) $session = 'AM';
        elseif ($now->hour < 18) $session = 'PM';
        else $session = 'OT';
    }

    // ✅ Enforce session‑appropriate time windows
    if ($session === 'AM' && $now->hour >= 12) {
        return redirect()->back()->with('error', 'AM time-in is only allowed before 12:00 PM.');
    }
    if ($session === 'PM' && ($now->hour < 12 || $now->hour >= 17)) {
        return redirect()->back()->with('error', 'PM time-in is only allowed between 12:00 PM and 5:00 PM.');
    }
    if ($session === 'OT' && $now->hour < 17) {
        return redirect()->back()->with('error', 'OT time-in is only allowed after 5:00 PM.');
    }

    // Determine cut-off time for this session (based on current date)
    $cutoff = match($session) {
        'AM' => Carbon::today()->setTime(11, 50),
        'PM' => Carbon::today()->setTime(16, 50),
        'OT' => null,
        default => null,
    };

    if ($cutoff && $now->gt($cutoff)) {
        return redirect()->back()
            ->with('error', "Cannot record {$session} time-in after " . $cutoff->format('h:i A') . ". The session has ended.");
    }

    $attendance = Attendance::where('student_id', $student->id)
        ->where('internship_id', $internship->id)
        ->where('date', $today)
        ->where('session', $session)
        ->first();

    if ($attendance && $attendance->time_in !== null) {
        return redirect()->back()
            ->with('error', "{$session} time-in already recorded for {$student->name} today.");
    }

    $lateHour = match($session) {
        'AM' => 8,
        'PM' => 13,
        'OT' => 18,
        default => 12
    };
    $status = $now->hour >= $lateHour ? 'late' : 'present';

    if ($attendance) {
        $attendance->update(['time_in' => $now, 'status' => $status]);
    } else {
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

    return redirect()->back()
        ->with('success', "{$session} Time In recorded for {$student->name} at " . $now->format('h:i A'));
}

  public function timeOut(Request $request, User $student)
{
    $teacherId  = auth()->id();
    $internship = Internship::where('student_id', $student->id)
        ->where('teacher_id', $teacherId)->first();

    if (!$internship) {
        return redirect()->back()->with('error', 'Student not under your supervision.');
    }

    $now     = Carbon::now();
    $today   = $now->toDateString();
    $session = strtoupper($request->input('session', ''));

    // If session not provided or invalid, auto-detect
    if (!in_array($session, ['AM', 'PM', 'OT'])) {
        if ($now->hour < 12) $session = 'AM';
        elseif ($now->hour < 18) $session = 'PM';
        else $session = 'OT';
    }

    // Override with the exact button value if it's valid (teacher's choice)
    $buttonSession = strtoupper($request->input('session', ''));
    if (in_array($buttonSession, ['AM', 'PM', 'OT'])) {
        $session = $buttonSession;
    }

    // Find the open attendance record for that session
    $attendance = Attendance::where('student_id', $student->id)
        ->where('internship_id', $internship->id)
        ->where('date', $today)
        ->where('session', $session)
        ->whereNull('time_out')
        ->first();

    if (!$attendance) {
        return redirect()->back()
            ->with('error', "No open {$session} time-in found for {$student->name} today.");
    }

    $timeIn = Carbon::parse($attendance->time_in);
    $hoursWorked = round(abs($timeIn->diffInMinutes($now)) / 60, 2);

    // ✅ Log after variables are defined
    \Log::info('TimeOut Debug', [
        'student' => $student->name,
        'session' => $session,
        'attendance_id' => $attendance->id,
        'time_in' => $attendance->time_in,
        'now' => $now,
        'diff_minutes' => $timeIn->diffInMinutes($now),
        'hours_worked' => $hoursWorked,
    ]);

    $attendance->update([
        'time_out'     => $now,
        'hours_worked' => $hoursWorked,
    ]);

    $this->recalcInternshipHours($internship);

    return redirect()->back()
        ->with('success', "{$session} Time Out recorded for {$student->name} at " . $now->format('h:i A') . " ({$hoursWorked} hrs)");
}

    /**
     * Recalculate and save internship total_hours_rendered from all attendance records.
     */
    private function recalcInternshipHours(Internship $internship): void
    {
        $internship->total_hours_rendered = $internship->attendances()->sum('hours_worked');
        $internship->save();
    }
}