<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
  
    public function index()
    {
        $students = User::students()->latest()->paginate(10);
        $teachers = User::teachers()->latest()->paginate(10);
        $admins = User::admins()->latest()->paginate(10);
        
        return view('admin.users.index', compact('students', 'teachers', 'admins'));
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
            'role' => 'required|in:admin,teacher,student',
            'student_id' => 'nullable|required_if:role,student|unique:users',
            'teacher_id' => 'nullable|required_if:role,teacher|unique:users',  // Changed from employee_id
            'department' => 'nullable|string',
            'course' => 'nullable|required_if:role,student|string',
            'year_level' => 'nullable|required_if:role,student|integer|min:1|max:4',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        
        User::create($validated);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
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

        // Add role-specific validation
        if ($user->role === 'student') {
            $rules['student_id'] = 'nullable|string|unique:users,student_id,' . $user->id;
            $rules['course'] = 'nullable|string';
            $rules['year_level'] = 'nullable|integer|min:1|max:4';
        } elseif ($user->role === 'teacher') {
            $rules['teacher_id'] = 'nullable|string|unique:users,teacher_id,' . $user->id;  // Changed from employee_id
        }

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        $validated = $request->validate($rules);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }
        
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }
}