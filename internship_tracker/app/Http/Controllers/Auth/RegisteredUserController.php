<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentQRCode;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:student,teacher'],
            'student_id' => ['required_if:role,student', 'nullable', 'string', 'unique:users,student_id'],
            'teacher_id' => ['required_if:role,teacher', 'nullable', 'string', 'unique:users,teacher_id'],
            'course' => ['required_if:role,student', 'nullable', 'string'],
            'year_level' => ['required_if:role,student', 'nullable', 'integer', 'min:1', 'max:4'],
            'department' => ['nullable', 'string'],
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'department' => $request->department,
        ];

        // Add role-specific fields
        if ($request->role === 'student') {
            $userData['student_id'] = $request->student_id;
            $userData['course'] = $request->course;
            $userData['year_level'] = $request->year_level;
        } elseif ($request->role === 'teacher') {
            $userData['teacher_id'] = $request->teacher_id;
        }

        // Create the user
        $user = User::create($userData);

        // If user is a student, generate QR code automatically
        if ($user->isStudent()) {
            StudentQRCode::create([
                'student_id' => $user->id,
                'qr_code' => $this->generateUniqueQRCode(),
                'status' => 'active'
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        // Redirect based on role
        return match($user->role) {
            'admin' => redirect()->route('admin.dashboard')->with('success', 'Welcome to the Internship Tracker System!'),
            'teacher' => redirect()->route('teacher.dashboard')->with('success', 'Welcome to the Internship Tracker System!'),
            default => redirect()->route('student.dashboard')->with('success', 'Welcome to the Internship Tracker System! Your QR code has been generated.'),
        };
    }

    /**
     * Generate unique QR code for student
     */
    private function generateUniqueQRCode(): string
    {
        do {
            $code = 'STU_' . strtoupper(uniqid());
        } while (StudentQRCode::where('qr_code', $code)->exists());
        
        return $code;
    }
}