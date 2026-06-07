{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-white" style="background-color: #216699;">
                    <h4 class="mb-0">{{ __('Create New Account') }}</h4>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Full Name') }} <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name') }}" required autofocus>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="role" class="col-md-4 col-form-label text-md-end">{{ __('Register As') }} <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <select id="role" class="form-control @error('role') is-invalid @enderror" name="role" required>
                                    <option value="">Select Role</option>
                                    <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                                    <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>Teacher</option>
                                </select>
                                @error('role')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Student Fields --}}
                        <div id="studentFields" class="role-fields" style="display: none;">
                            <div class="row mb-3">
                                <label for="student_id" class="col-md-4 col-form-label text-md-end">{{ __('Student ID') }} <span class="text-danger">*</span></label>
                                <div class="col-md-6">
                                    <input id="student_id" type="text" class="form-control @error('student_id') is-invalid @enderror" 
                                           name="student_id" value="{{ old('student_id') }}" placeholder="e.g., 2024-001">
                                    <small class="text-muted">Enter your official student ID number</small>
                                    @error('student_id')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="course" class="col-md-4 col-form-label text-md-end">{{ __('Course') }} <span class="text-danger">*</span></label>
                                <div class="col-md-6">
                                    <select id="course" class="form-control @error('course') is-invalid @enderror" name="course">
                                        <option value="">Select Course</option>
                                        <option value="BSIT" {{ old('course') == 'BSIT' ? 'selected' : '' }}>BS Information Technology</option>
                                        <option value="BSCS" {{ old('course') == 'BSCS' ? 'selected' : '' }}>BS Computer Science</option>
                                        <option value="BSIS" {{ old('course') == 'BSIS' ? 'selected' : '' }}>BS Information Systems</option>
                                        <option value="BSECE" {{ old('course') == 'BSECE' ? 'selected' : '' }}>BS Electronics Engineering</option>
                                    </select>
                                    @error('course')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="year_level" class="col-md-4 col-form-label text-md-end">{{ __('Year Level') }} <span class="text-danger">*</span></label>
                                <div class="col-md-6">
                                    <select id="year_level" class="form-control @error('year_level') is-invalid @enderror" name="year_level">
                                        <option value="">Select Year Level</option>
                                        <option value="1" {{ old('year_level') == '1' ? 'selected' : '' }}>1st Year</option>
                                        <option value="2" {{ old('year_level') == '2' ? 'selected' : '' }}>2nd Year</option>
                                        <option value="3" {{ old('year_level') == '3' ? 'selected' : '' }}>3rd Year</option>
                                        <option value="4" {{ old('year_level') == '4' ? 'selected' : '' }}>4th Year</option>
                                    </select>
                                    @error('year_level')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Teacher Fields --}}
                        <div id="teacherFields" class="role-fields" style="display: none;">
                            <div class="row mb-3">
                                <label for="teacher_id" class="col-md-4 col-form-label text-md-end">{{ __('Teacher ID') }} <span class="text-danger">*</span></label>
                                <div class="col-md-6">
                                    <input id="teacher_id" type="text" class="form-control @error('teacher_id') is-invalid @enderror" 
                                           name="teacher_id" value="{{ old('teacher_id') }}" placeholder="e.g., TCH-2024-001">
                                    <small class="text-muted">Enter your official teacher ID number</small>
                                    @error('teacher_id')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="department" class="col-md-4 col-form-label text-md-end">{{ __('Department') }}</label>
                            <div class="col-md-6">
                                <select id="department" class="form-control @error('department') is-invalid @enderror" name="department">
                                    <option value="">Select Department</option>
                                    <option value="Computer Science" {{ old('department') == 'Computer Science' ? 'selected' : '' }}>Computer Science</option>
                                    <option value="Information Technology" {{ old('department') == 'Information Technology' ? 'selected' : '' }}>Information Technology</option>
                                    <option value="Engineering" {{ old('department') == 'Engineering' ? 'selected' : '' }}>Engineering</option>
                                    <option value="Business" {{ old('department') == 'Business' ? 'selected' : '' }}>Business</option>
                                    <option value="Arts and Sciences" {{ old('department') == 'Arts and Sciences' ? 'selected' : '' }}>Arts and Sciences</option>
                                </select>
                                @error('department')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }} <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                       name="password" required>
                                <small class="text-muted">Password must be at least 8 characters</small>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }} <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary" style="background-color: #216699; border-color: #216699;">
                                    <i class="fas fa-user-plus"></i> {{ __('Register') }}
                                </button>
                                <a href="{{ route('login') }}" class="btn btn-link">
                                    {{ __('Already have an account? Login') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .role-fields {
        border-left: 3px solid #216699;
        padding-left: 15px;
        margin-bottom: 10px;
        background-color: #f8f9fa;
        border-radius: 5px;
    }
    .text-danger {
        color: #dc3545 !important;
    }
    .card-header {
        background-color: #216699;
        border-bottom: 2px solid #ede432;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const studentFields = document.getElementById('studentFields');
    const teacherFields = document.getElementById('teacherFields');
    const studentIdInput = document.getElementById('student_id');
    const courseSelect = document.getElementById('course');
    const yearLevelSelect = document.getElementById('year_level');
    const teacherIdInput = document.getElementById('teacher_id');
    
    function toggleRoleFields() {
        const selectedRole = roleSelect.value;
        
        // Hide all role-specific fields first
        studentFields.style.display = 'none';
        teacherFields.style.display = 'none';
        
        // Reset required attributes
        if (studentIdInput) studentIdInput.required = false;
        if (courseSelect) courseSelect.required = false;
        if (yearLevelSelect) yearLevelSelect.required = false;
        if (teacherIdInput) teacherIdInput.required = false;
        
        // Show and set required based on selected role
        if (selectedRole === 'student') {
            studentFields.style.display = 'block';
            if (studentIdInput) studentIdInput.required = true;
            if (courseSelect) courseSelect.required = true;
            if (yearLevelSelect) yearLevelSelect.required = true;
        } else if (selectedRole === 'teacher') {
            teacherFields.style.display = 'block';
            if (teacherIdInput) teacherIdInput.required = true;
        }
    }
    
    // Initial call
    toggleRoleFields();
    
    // Add event listener
    roleSelect.addEventListener('change', toggleRoleFields);
});
</script>
@endsection