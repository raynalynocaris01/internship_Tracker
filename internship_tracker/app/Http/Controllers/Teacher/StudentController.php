<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentQRCode;
use App\Models\Internship;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index()
    {
        $teacherId = auth()->id();
        
        $students = User::whereHas('internships', function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        })->with(['internships' => function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId)->with('subject');
        }, 'attendances'])->get();
        
        return view('teacher.students.index', compact('students'));
    }
    
    public function create()
    {
        return view('teacher.students.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'student_id' => 'required|string|unique:users',
            'course' => 'required|string|in:BSIT,BSCS,BSIS,BSECE',
            'year_level' => 'required|integer|min:1|max:4',
            'department' => 'nullable|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'student',
            'student_id' => $validated['student_id'],
            'course' => $validated['course'],
            'year_level' => $validated['year_level'],
            'department' => $validated['department'] ?? 'Information Technology',
        ]);

        // Generate QR code for the student
        StudentQRCode::create([
            'student_id' => $user->id,
            'qr_code' => $this->generateUniqueQRCode(),
            'status' => 'active'
        ]);

        return redirect()->route('teacher.students.index')
            ->with('success', 'Student account created successfully. QR code has been generated.');
    }
    
    public function show(User $student)
    {
        $teacherId = auth()->id();
        
        $internship = $student->internships()
            ->where('teacher_id', $teacherId)
            ->with('subject')
            ->first();
        
        $attendances = Attendance::where('student_id', $student->id)
            ->with('internship.subject')
            ->latest('date')
            ->paginate(20);
            
        $totalHours = Attendance::where('student_id', $student->id)->sum('hours_worked');
        $totalDays = Attendance::where('student_id', $student->id)->count();
        $progress = $internship ? $internship->progress : 0;
        
        return view('teacher.students.show', compact('student', 'internship', 'attendances', 'totalHours', 'totalDays', 'progress'));
    }
    
    private function generateUniqueQRCode()
    {
        do {
            $code = 'STU_' . strtoupper(uniqid());
        } while (StudentQRCode::where('qr_code', $code)->exists());
        
        return $code;
    }
}