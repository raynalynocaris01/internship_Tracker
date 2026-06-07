<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\StudentSubjectEnrollment;
use App\Models\StudentQRCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

// Use only one QR code package - choose based on what you have installed
// Option 1: If using SimpleSoftwareIO (requires GD extension)
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendanceController extends Controller
{
    public function dashboard()
    {
        $student = auth()->user();
        
        // Get active enrollment
        $enrollment = StudentSubjectEnrollment::where('student_id', $student->id)
            ->where('status', 'enrolled')
            ->with('subject', 'teacher')
            ->first();
        
        if (!$enrollment) {
            return view('student.dashboard', [
                'noEnrollment' => true,
                'message' => 'You are not enrolled in any subject. Please contact your administrator.'
            ]);
        }
        
        // Get or create QR code
        $qrCode = StudentQRCode::firstOrCreate(
            ['student_id' => $student->id],
            ['qr_code' => $this->generateUniqueQRCode(), 'status' => 'active']
        );
        
        // Today's attendance
        $todayAttendance = Attendance::where('student_id', $student->id)
            ->where('enrollment_id', $enrollment->id)
            ->whereDate('date', Carbon::today())
            ->first();
        
        // Recent attendance (last 10 records)
        $recentAttendance = Attendance::where('student_id', $student->id)
            ->where('enrollment_id', $enrollment->id)
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();
        
        // Statistics
        $totalHours = Attendance::where('student_id', $student->id)
            ->where('enrollment_id', $enrollment->id)
            ->sum('hours_worked');
            
        $totalDays = Attendance::where('student_id', $student->id)
            ->where('enrollment_id', $enrollment->id)
            ->count();
            
        $progress = $enrollment->progress;
        $requiredHours = $enrollment->subject->required_hours;
        $remainingHours = max($requiredHours - $totalHours, 0);
        
        // Generate QR code image
        $qrCodeImage = $this->generateQRCodeImage($qrCode->qr_code);
        
        return view('student.dashboard', compact(
            'student', 'enrollment', 'qrCode', 'qrCodeImage',
            'todayAttendance', 'recentAttendance', 'totalHours', 
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
            
            // Get active enrollment
            $enrollment = StudentSubjectEnrollment::where('student_id', $student->id)
                ->where('status', 'enrolled')
                ->first();
            
            if (!$enrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active enrollment found.'
                ], 400);
            }
            
            // Check if already timed in today
            $existingAttendance = Attendance::where('student_id', $student->id)
                ->where('enrollment_id', $enrollment->id)
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
                'subject_id' => $enrollment->subject_id,
                'enrollment_id' => $enrollment->id,
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
            
            // Update enrollment total hours
            if ($attendance->enrollment) {
                $totalHours = Attendance::where('enrollment_id', $attendance->enrollment_id)->sum('hours_worked');
                $attendance->enrollment->total_hours_rendered = $totalHours;
                $attendance->enrollment->save();
            }
        }
        
        return $attendance->hours_worked;
    }
    
    public function attendanceHistory()
    {
        $student = auth()->user();
        
        $attendances = Attendance::where('student_id', $student->id)
            ->with('subject')
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
}