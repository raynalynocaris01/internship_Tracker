<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentQRCode;
use App\Models\Internship;
use App\Models\Subject;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class StudentController extends Controller
{
   public function index(Request $request)
{
    $teacherId = auth()->id();
    $status = $request->get('status', 'all');
    
    $query = User::where('role', 'student')
        ->whereHas('internships', function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        })
        ->with(['internships' => function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId)->with('subject');
        }, 'attendances']);
    
    // Apply status filter
    if ($status == 'active') {
        $query->whereHas('internships', function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId)->where('status', 'active');
        });
    } elseif ($status == 'completed') {
        $query->whereHas('internships', function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId)->where('status', 'completed');
        });
    } elseif ($status == 'pending') {
        $query->whereHas('internships', function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId)->where('status', 'pending');
        });
    } elseif ($status == 'no_internship') {
        $query = User::where('role', 'student')
            ->whereDoesntHave('internships', function($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            });
    }
    
    $students = $query->latest()->paginate(15);
    
    // Statistics for tabs
    $stats = [
        'total' => User::where('role', 'student')
            ->whereHas('internships', function($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            })->count(),
        'active' => Internship::where('teacher_id', $teacherId)->where('status', 'active')->count(),
        'completed' => Internship::where('teacher_id', $teacherId)->where('status', 'completed')->count(),
        'pending' => Internship::where('teacher_id', $teacherId)->where('status', 'pending')->count(),
        'no_internship' => User::where('role', 'student')
            ->whereDoesntHave('internships', function($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            })->count(),
    ];
    
    $subjects = Subject::where('status', 'active')->get();
    
    return view('teacher.students.index', compact('students', 'stats', 'subjects', 'status'));
}


    public function create()
    {
        $subjects = Subject::where('status', 'active')->get();
        return view('teacher.students.create', compact('subjects'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'student_id' => 'required|string|unique:users',
            'course' => 'required|string|in:BSIT,BSCS,BSIS,BSECE',
            'year_level' => 'required|integer|min:1|max:4',
            'password' => 'required|string|min:8|confirmed',
            'subject_id' => 'required|exists:subjects,id',
            'company_name' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
        ]);

        // Create student account
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'student',
            'student_id' => $validated['student_id'],
            'course' => $validated['course'],
            'year_level' => $validated['year_level'],
            'department' => 'Information Technology',
        ]);

        // Generate QR code
        StudentQRCode::create([
            'student_id' => $user->id,
            'qr_code' => $this->generateUniqueQRCode(),
            'status' => 'active'
        ]);

        // Create internship assignment
        Internship::create([
            'student_id' => $user->id,
            'subject_id' => $validated['subject_id'],
            'teacher_id' => auth()->id(),
            'company_name' => $validated['company_name'],
            'position' => $validated['position'],
            'start_date' => $validated['start_date'] ?? Carbon::today(),
            'status' => 'active',
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
        $totalDays = Attendance::where('student_id', $student->id)->count();
        $progress = $internship->progress;
        
        return view('teacher.students.show', compact('student', 'internship', 'attendances', 'totalHours', 'totalDays', 'progress'));
    }
    
    private function generateUniqueQRCode()
    {
        do {
            $code = 'STU_' . strtoupper(uniqid());
        } while (StudentQRCode::where('qr_code', $code)->exists());
        
        return $code;
    }
    /**
 * Bulk assign internships to multiple students
 */
public function bulkAssign(Request $request)
{
    $request->validate([
        'student_ids' => 'required|array|min:1',
        'student_ids.*' => 'exists:users,id',
        'subject_id' => 'required|exists:subjects,id',
        'company_name' => 'nullable|string|max:255',
        'position' => 'nullable|string|max:255',
        'start_date' => 'nullable|date',
    ]);
    
    $teacherId = auth()->id();
    $assignedCount = 0;
    $skippedCount = 0;
    
    foreach ($request->student_ids as $studentId) {
        // Check if student already has an active internship
        $existing = Internship::where('student_id', $studentId)
            ->where('status', 'active')
            ->first();
        
        if ($existing) {
            $skippedCount++;
            continue;
        }
        
        // Create internship assignment
        Internship::create([
            'student_id' => $studentId,
            'subject_id' => $request->subject_id,
            'teacher_id' => $teacherId,
            'company_name' => $request->company_name,
            'position' => $request->position,
            'start_date' => $request->start_date ?? Carbon::today(),
            'status' => 'active',
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
}