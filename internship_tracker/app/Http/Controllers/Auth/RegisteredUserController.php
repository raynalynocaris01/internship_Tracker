<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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
            'student_id' => ['required_if:role,student', 'nullable', 'string', 'unique:users'],
            'teacher_id' => ['required_if:role,teacher', 'nullable', 'string', 'unique:users'],  // Changed from employee_id
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
            $userData['teacher_id'] = $request->teacher_id;  // Changed from employee_id
        }

        $user = User::create($userData);

        event(new Registered($user));

        Auth::login($user);

        // Redirect based on role
        return match($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'teacher' => redirect()->route('teacher.dashboard'),
            default => redirect()->route('student.dashboard'),
        };
    }
}