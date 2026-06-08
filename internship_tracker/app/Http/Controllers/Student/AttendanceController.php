<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Internship;  // Changed from StudentSubjectEnrollment
use App\Models\StudentQRCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Models\SubjectQRCode;
use Zxing\QrReader;
// QR Code package
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendanceController extends Controller
{
    public function dashboard()
    {
        $student = auth()->user();
        
        // Get active internship (changed from enrollment)
        $internship = Internship::where('student_id', $student->id)
            ->where('status', 'active')  // Changed from 'enrolled'
            ->with('subject', 'teacher')
            ->first();
        
        if (!$internship) {
            return view('student.dashboard', [
                'noInternship' => true,  // Changed variable name
                'message' => 'You have no active internship. Please contact your administrator.'
            ]);
        }
        
        // Get or create QR code
        $qrCode = StudentQRCode::firstOrCreate(
            ['student_id' => $student->id],
            ['qr_code' => $this->generateUniqueQRCode(), 'status' => 'active']
        );
        
        // Today's attendance (separate sessions)
        $todayAM = Attendance::where('student_id', $student->id)
            ->where('internship_id', $internship->id)
            ->whereDate('date', Carbon::today())
            ->where('session', 'AM')
            ->first();

        $todayPM = Attendance::where('student_id', $student->id)
            ->where('internship_id', $internship->id)
            ->whereDate('date', Carbon::today())
            ->where('session', 'PM')
            ->first();
        
        // Recent attendance (last 10 records)
        $recentAttendance = Attendance::where('student_id', $student->id)
            ->where('internship_id', $internship->id)  // Changed from enrollment_id
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();
        
        // Statistics
        $totalHours = Attendance::where('student_id', $student->id)
            ->where('internship_id', $internship->id)  // Changed from enrollment_id
            ->sum('hours_worked');
            
        $totalDays = Attendance::where('student_id', $student->id)
            ->where('internship_id', $internship->id)  // Changed from enrollment_id
            ->count();
            
        $progress = $internship->progress;  // Changed from $enrollment->progress
        $requiredHours = $internship->subject->required_hours;
        $remainingHours = max($requiredHours - $totalHours, 0);
        
        // Generate QR code image
        $qrCodeImage = $this->generateQRCodeImage($qrCode->qr_code);
        
        return view('student.dashboard', compact(
            'student', 'internship', 'qrCode', 'qrCodeImage',  
            'todayAM', 'todayPM',  
       'recentAttendance', 'totalHours', 
            'totalDays', 'progress', 'requiredHours', 'remainingHours'
        ));
    }
    
    /**
     * Generate QR code image - Works with or without GD extension
     */
    private function generateQRCodeImage($qrData)
    {
        // Try to use SimpleSoftwareIO if GD is enabled
        if (extension_loaded('gd')) {
            try {
                return QrCode::size(250)->generate($qrData);
            } catch (\Exception $e) {
                Log::warning('QR Code generation with GD failed: ' . $e->getMessage());
            }
        }
        
        // Fallback: Use free QR API (no GD required)
        return $this->getQRCodeFromAPI($qrData);
    }
    
    /**
     * Fallback: Get QR code from free online API
     */
    private function getQRCodeFromAPI($qrData)
    {
        $size = 250;
        $url = "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data=" . urlencode($qrData);
        return '<img src="' . $url . '" alt="QR Code" class="img-fluid">';
    }
    
    public function timeIn(Request $request)
    {
        try {
            $student = auth()->user();
            $qrData = $request->input('qr_data');
            
            if (!$qrData) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR code data is required.'
                ], 400);
            }
            
            // Verify QR code
            $validQR = StudentQRCode::where('student_id', $student->id)
                ->where('qr_code', $qrData)
                ->where('status', 'active')
                ->first();
            
            if (!$validQR) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR code.'
                ], 400);
            }
            
            // Get active internship (changed from enrollment)
            $internship = Internship::where('student_id', $student->id)
                ->where('status', 'active')  // Changed from 'enrolled'
                ->first();
            
            if (!$internship) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active internship found.'
                ], 400);
            }
            
            // Check if already timed in today
            $existingAttendance = Attendance::where('student_id', $student->id)
                ->where('internship_id', $internship->id)  // Changed from enrollment_id
                ->whereDate('date', Carbon::today())
                ->first();
            
            if ($existingAttendance && $existingAttendance->time_out === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are already timed in. Please time out first.'
                ], 400);
            }
            
            if ($existingAttendance && $existingAttendance->time_out !== null) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already completed your attendance for today.'
                ], 400);
            }
            
            // Create attendance record
            $attendance = Attendance::create([
                'student_id' => $student->id,
                'subject_id' => $internship->subject_id,  // Changed from enrollment->subject_id
                'internship_id' => $internship->id,  // Changed from enrollment_id
                'date' => Carbon::today(),
                'time_in' => Carbon::now(),
                'qr_code_scanned' => $qrData,
                'status' => 'present'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Time in recorded successfully at ' . Carbon::now()->format('h:i A'),
                'data' => $attendance
            ]);
            
        } catch (\Exception $e) {
            Log::error('Time In Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please try again.'
            ], 500);
        }
    }
    
    public function timeOut(Request $request)
    {
        try {
            $student = auth()->user();
            
            $attendance = Attendance::where('student_id', $student->id)
                ->whereDate('date', Carbon::today())
                ->first();
            
            if (!$attendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'No time in record found for today.'
                ], 400);
            }
            
            if ($attendance->time_out !== null) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already timed out for today.'
                ], 400);
            }
            
            $attendance->time_out = Carbon::now();
            $attendance->save();
            
            // Calculate hours worked
            $this->calculateAttendanceHours($attendance);
            
            return response()->json([
                'success' => true,
                'message' => 'Time out recorded successfully at ' . Carbon::now()->format('h:i A'),
                'hours_worked' => $attendance->hours_worked
            ]);
            
        } catch (\Exception $e) {
            Log::error('Time Out Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please try again.'
            ], 500);
        }
    }
    
    /**
     * Calculate hours worked for an attendance record
     */
    private function calculateAttendanceHours($attendance)
    {
        if ($attendance->time_in && $attendance->time_out) {
            $timeIn = Carbon::parse($attendance->time_in);
            $timeOut = Carbon::parse($attendance->time_out);
            $diffInSeconds = $timeOut->diffInSeconds($timeIn);
            
            // Calculate hours
            $hours = $diffInSeconds / 3600;
            
            // Subtract 1 hour for lunch if worked more than 5 hours
            if ($hours > 5) {
                $hours -= 1;
            }
            
            $attendance->hours_worked = round($hours, 2);
            $attendance->save();
            
            // Update internship total hours (changed from enrollment)
            if ($attendance->internship) {
                $totalHours = Attendance::where('internship_id', $attendance->internship_id)->sum('hours_worked');
                $attendance->internship->total_hours_rendered = $totalHours;
                $attendance->internship->save();
                
                // Check if internship is completed
                if ($totalHours >= $attendance->internship->subject->required_hours) {
                    $attendance->internship->status = 'completed';
                    $attendance->internship->completion_date = Carbon::today();
                    $attendance->internship->save();
                }
            }
        }
        
        return $attendance->hours_worked;
    }
    
    public function attendanceHistory()
    {
        $student = auth()->user();
        
        $attendances = Attendance::where('student_id', $student->id)
            ->with(['internship.subject'])  // Changed from 'subject'
            ->orderBy('date', 'desc')
            ->paginate(20);
            
        $totalHours = Attendance::where('student_id', $student->id)->sum('hours_worked');
        $totalDays = Attendance::where('student_id', $student->id)->count();
        
        return view('student.history', compact('attendances', 'totalHours', 'totalDays'));
    }
    
    /**
     * Generate unique QR code for student
     */
    private function generateUniqueQRCode()
    {
        do {
            $code = 'STU_' . strtoupper(uniqid());
        } while (StudentQRCode::where('qr_code', $code)->exists());
        
        return $code;
    }
    
    /**
     * Download QR code as image
     */
    public function downloadQRCode()
    {
        $student = auth()->user();
        $qrCode = StudentQRCode::where('student_id', $student->id)->first();
        
        if (!$qrCode) {
            return back()->with('error', 'QR Code not found.');
        }
        
        // Use the fallback API method for download
        $size = 300;
        $url = "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data=" . urlencode($qrCode->qr_code);
        
        // Download the image
        $imageContent = @file_get_contents($url);
        
        if (!$imageContent) {
            return back()->with('error', 'Failed to generate QR code image.');
        }
        
        return response($imageContent)
            ->withHeaders([
                'Content-Type' => 'image/png',
                'Content-Disposition' => 'attachment; filename="qrcode.png"',
            ]);
    }

    public function scan(string $token)
    {
        $student = auth()->user();
 
        // Validate the QR token
        $qrCode = \App\Models\SubjectQRCode::where('qr_token', $token)
            ->where('is_active', true)
            ->whereDate('valid_date', \Carbon\Carbon::today())
            ->with(['subject', 'section'])
            ->first();
 
        if (!$qrCode) {
            return view('student.scan', [
                'success' => false,
                'error'   => 'This QR code is invalid or has expired. Ask your teacher to refresh it.',
                'qrCode'  => null,
            ]);
        }
 
        // Check if this student has an active internship for this subject+section
        $internship = \App\Models\Internship::where('student_id', $student->id)
            ->where('subject_id', $qrCode->subject_id)
            ->where('section_id', $qrCode->section_id)
            ->where('status', 'active')
            ->first();
 
        if (!$internship) {
            // Not registered — show a "not enrolled" message
            return view('student.scan', [
                'success'    => false,
                'notEnrolled'=> true,
                'error'      => "You are not registered for {$qrCode->subject->code} - {$qrCode->subject->name} ({$qrCode->section->name}). Please contact your teacher.",
                'qrCode'     => $qrCode,
                'student'    => $student,
            ]);
        }
 
        $now     = \Carbon\Carbon::now();
        $today   = $now->toDateString();
        $session = $qrCode->session; // AM or PM
 
        // Check for existing record
        $existing = \App\Models\Attendance::where('student_id', $student->id)
            ->where('internship_id', $internship->id)
            ->where('date', $today)
            ->where('session', $session)
            ->first();
 
        // Already fully done (time_in + time_out recorded)
        if ($existing && $existing->time_in && $existing->time_out) {
            return view('student.scan', [
                'success'    => true,
                'alreadyDone'=> true,
                'message'    => "You have already completed your {$session} attendance for today.",
                'qrCode'     => $qrCode,
                'attendance' => $existing,
                'student'    => $student,
            ]);
        }
 
        // Clocked in but not out → record time out
        if ($existing && $existing->time_in && !$existing->time_out) {
            $hoursWorked = round($now->diffInMinutes(\Carbon\Carbon::parse($existing->time_in)) / 60, 2);
            $existing->update([
                'time_out'     => $now,
                'hours_worked' => $hoursWorked,
            ]);
 
            // Update internship total hours
            $internship->total_hours_rendered = $internship->attendances()->sum('hours_worked');
            $internship->save();
 
            return view('student.scan', [
                'success'    => true,
                'action'     => 'timeout',
                'message'    => "{$session} Time Out recorded at " . $now->format('h:i A') . " ({$hoursWorked} hrs)",
                'qrCode'     => $qrCode,
                'attendance' => $existing->fresh(),
                'student'    => $student,
            ]);
        }
 
        // No record yet → record time in
        $lateHour  = $session === 'AM' ? 8 : 13;
        $status    = $now->hour >= $lateHour ? 'late' : 'present';
 
        $attendance = \App\Models\Attendance::create([
            'student_id'    => $student->id,
            'internship_id' => $internship->id,
            'subject_id'    => $internship->subject_id,
            'date'          => $today,
            'session'       => $session,
            'time_in'       => $now,
            'status'        => $status,
        ]);
 
        return view('student.scan', [
            'success'    => true,
            'action'     => 'timein',
            'message'    => "{$session} Time In recorded at " . $now->format('h:i A'),
            'qrCode'     => $qrCode,
            'attendance' => $attendance,
            'student'    => $student,
        ]);
    }


public function showScanner()
{
    return view('student.scanner');
}
}