<?php
// app/Http/Controllers/Teacher/SubjectQRCodeController.php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\SubjectQRCode;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Internship;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectQRCodeController extends Controller
{
    /**
     * Generate (or retrieve existing) QR code for a subject+section+session today.
     * Called when teacher clicks "Show QR" on the attendance page.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'section_id' => 'required|exists:sections,id',
            'session'    => 'required|in:AM,PM,OT',
        ]);

        $teacherId = auth()->id();
        $today     = Carbon::today();

        // Verify this teacher owns this subject+section assignment
        $assigned = DB::table('subject_section')
            ->where('teacher_id', $teacherId)
            ->where('subject_id', $request->subject_id)
            ->where('section_id', $request->section_id)
            ->exists();

        if (!$assigned) {
            return redirect()->back()->with('error', 'You are not assigned to this subject/section.');
        }

        // Upsert: if a QR already exists for today+session, reuse it; otherwise create fresh
        $qrCode = SubjectQRCode::firstOrCreate(
            [
                'subject_id' => $request->subject_id,
                'section_id' => $request->section_id,
                'session'    => $request->session,
                'valid_date' => $today,
            ],
            [
                'teacher_id' => $teacherId,
                'qr_token'   => SubjectQRCode::generateToken(),
                'is_active'  => true,
            ]
        );

        // Re-activate if it was previously deactivated today
        if (!$qrCode->is_active) {
            $qrCode->update(['is_active' => true, 'teacher_id' => $teacherId]);
        }

        return redirect()->route('teacher.qrcode.show', $qrCode);
    }

    /**
     * Display the QR code on screen for students to scan.
     */
    public function show(SubjectQRCode $qrCode)
    {
        if ($qrCode->teacher_id !== auth()->id()) {
            abort(403);
        }

        if (!$qrCode->isValidToday()) {
            return redirect()->route('teacher.attendance.index')
                ->with('error', 'This QR code has expired. Please generate a new one.');
        }

        // The URL students will scan — points to the student scan endpoint
        $scanUrl = route('student.attendance.scan', ['token' => $qrCode->qr_token]);

        return view('teacher.qrcodes.show', compact('qrCode', 'scanUrl'));
    }

    /**
     * Deactivate a QR code early (teacher closes the session).
     */
    public function deactivate(SubjectQRCode $qrCode)
    {
        if ($qrCode->teacher_id !== auth()->id()) {
            abort(403);
        }

        $qrCode->update(['is_active' => false]);

        return redirect()->route('teacher.attendance.index')
            ->with('success', 'QR code session closed.');
    }

    /**
     * Live poll endpoint — returns list of students who scanned in the last N seconds.
     * Called every few seconds by the teacher's show page via JS fetch.
     */
    public function recentScans(SubjectQRCode $qrCode)
    {
        if ($qrCode->teacher_id !== auth()->id()) {
            abort(403);
        }

        // Get internship IDs for this section+subject
        $internshipIds = Internship::where('subject_id', $qrCode->subject_id)
            ->where('section_id', $qrCode->section_id)
            ->pluck('id');

        // Students who have a session record for today
        $scanned = Attendance::whereIn('internship_id', $internshipIds)
            ->whereDate('date', Carbon::today())
            ->where('session', $qrCode->session)
            ->with('student')
            ->get()
            ->map(fn($a) => [
                'name'       => $a->student->name,
                'student_id' => $a->student->student_id,
                'time_in'    => $a->time_in ? Carbon::parse($a->time_in)->format('h:i A') : null,
                'time_out'   => $a->time_out ? Carbon::parse($a->time_out)->format('h:i A') : null,
                'status'     => $a->status,
            ]);

        return response()->json(['scanned' => $scanned]);
    }
}