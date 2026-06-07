<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentQRCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->get('role', 'all');
        
        $query = User::query();
        
        if ($role !== 'all') {
            $query->where('role', $role);
        }
        
        $users = $query->latest()->paginate(15);
        
        // Get counts for each role (for the tabs)
        $totalStudents = User::where('role', 'student')->count();
        $totalTeachers = User::where('role', 'teacher')->count();
        $totalAdmins = User::where('role', 'admin')->count();
        
        return view('admin.users.index', compact('users', 'role', 'totalStudents', 'totalTeachers', 'totalAdmins'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,teacher,student',
            'student_id' => 'nullable|required_if:role,student|unique:users',
            'teacher_id' => 'nullable|required_if:role,teacher|unique:users',
            'department' => 'nullable|string',
            'course' => 'nullable|required_if:role,student|string',
            'year_level' => 'nullable|required_if:role,student|integer|min:1|max:4',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'department' => $validated['department'] ?? null,
        ];

        // Add role-specific fields
        if ($validated['role'] === 'student') {
            $userData['student_id'] = $validated['student_id'];
            $userData['course'] = $validated['course'];
            $userData['year_level'] = $validated['year_level'];
        } elseif ($validated['role'] === 'teacher') {
            $userData['teacher_id'] = $validated['teacher_id'];
        }

        $user = User::create($userData);

        // Generate QR code automatically for students
        if ($user->isStudent()) {
            StudentQRCode::create([
                'student_id' => $user->id,
                'qr_code' => $this->generateUniqueQRCode(),
                'status' => 'active'
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.' . ($user->isStudent() ? ' QR code has been generated.' : ''));
    }

    public function show(User $user)
    {
        $attendanceCount = 0;
        $totalHours = 0;
        $activeInternship = null;
        
        if ($user->isStudent()) {
            $attendanceCount = $user->attendances()->count();
            $totalHours = $user->attendances()->sum('hours_worked');
            $activeInternship = $user->internships()->active()->first();  // Added active internship
        }
        
        return view('admin.users.show', compact('user', 'attendanceCount', 'totalHours', 'activeInternship'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'department' => 'nullable|string',
        ];

        if ($user->isStudent()) {
            $rules['student_id'] = 'nullable|string|unique:users,student_id,' . $user->id;
            $rules['course'] = 'nullable|string';
            $rules['year_level'] = 'nullable|integer|min:1|max:4';
        } elseif ($user->isTeacher()) {
            $rules['teacher_id'] = 'nullable|string|unique:users,teacher_id,' . $user->id;
        }

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        $validated = $request->validate($rules);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'department' => $validated['department'] ?? $user->department,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        if ($user->isStudent()) {
            $updateData['student_id'] = $validated['student_id'] ?? $user->student_id;
            $updateData['course'] = $validated['course'] ?? $user->course;
            $updateData['year_level'] = $validated['year_level'] ?? $user->year_level;
        } elseif ($user->isTeacher()) {
            $updateData['teacher_id'] = $validated['teacher_id'] ?? $user->teacher_id;
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }
        
        // Check if student has attendance records
        if ($user->isStudent() && $user->attendances()->count() > 0) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot delete student with existing attendance records.');
        }
        
        // Check if teacher has assigned internships
        if ($user->isTeacher() && $user->supervisedInternships()->count() > 0) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot delete teacher with assigned internships.');
        }
        
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
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
}