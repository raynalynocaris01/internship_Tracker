{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Full Name') }}</label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus>
                                @error('name')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="role" class="col-md-4 col-form-label text-md-end">{{ __('Register As') }}</label>
                            <div class="col-md-6">
                                <select id="role" class="form-control @error('role') is-invalid @enderror" name="role" required>
                                    <option value="">Select Role</option>
                                    <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                                    <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>Teacher</option>
                                </select>
                                @error('role')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        {{-- Student Fields --}}
                        <div id="studentFields" class="role-fields" style="display: none;">
                            <div class="row mb-3">
                                <label for="student_id" class="col-md-4 col-form-label text-md-end">{{ __('Student ID') }}</label>
                                <div class="col-md-6">
                                    <input id="student_id" type="text" class="form-control @error('student_id') is-invalid @enderror" name="student_id" value="{{ old('student_id') }}" placeholder="e.g., 2024001">
                                    <small class="form-text text-muted">Enter your student ID number</small>
                                    @error('student_id')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="course" class="col-md-4 col-form-label text-md-end">{{ __('Course') }}</label>
                                <div class="col-md-6">
                                    <select id="course" class="form-control @error('course') is-invalid @enderror" name="course">
                                        <option value="">Select Course</option>
                                        <option value="BSIT" {{ old('course') == 'BSIT' ? 'selected' : '' }}>BS Information Technology</option>
                                        <option value="BSCS" {{ old('course') == 'BSCS' ? 'selected' : '' }}>BS Computer Science</option>
                                        <option value="BSIS" {{ old('course') == 'BSIS' ? 'selected' : '' }}>BS Information Systems</option>
                                        <option value="BSECE" {{ old('course') == 'BSECE' ? 'selected' : '' }}>BS Electronics Engineering</option>
                                    </select>
                                    @error('course')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="year_level" class="col-md-4 col-form-label text-md-end">{{ __('Year Level') }}</label>
                                <div class="col-md-6">
                                    <select id="year_level" class="form-control @error('year_level') is-invalid @enderror" name="year_level">
                                        <option value="">Select Year Level</option>
                                        <option value="1" {{ old('year_level') == '1' ? 'selected' : '' }}>1st Year</option>
                                        <option value="2" {{ old('year_level') == '2' ? 'selected' : '' }}>2nd Year</option>
                                        <option value="3" {{ old('year_level') == '3' ? 'selected' : '' }}>3rd Year</option>
                                        <option value="4" {{ old('year_level') == '4' ? 'selected' : '' }}>4th Year</option>
                                    </select>
                                    @error('year_level')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Teacher Fields --}}
                        <div id="teacherFields" class="role-fields" style="display: none;">
                            <div class="row mb-3">
                                <label for="teacher_id" class="col-md-4 col-form-label text-md-end">{{ __('Teacher ID') }}</label>
                                <div class="col-md-6">
                                    <input id="teacher_id" type="text" class="form-control @error('teacher_id') is-invalid @enderror" name="teacher_id" value="{{ old('teacher_id') }}" placeholder="e.g., 1001">
                                    <small class="form-text text-muted">Enter your teacher ID number</small>
                                    @error('teacher_id')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
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
                                </select>
                                @error('department')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>
                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                                <small class="form-text text-muted">Password must be at least 8 characters</small>
                                @error('password')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>
                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('role').addEventListener('change', function() {
    const studentFields = document.getElementById('studentFields');
    const teacherFields = document.getElementById('teacherFields');
    
    // Hide both first
    studentFields.style.display = 'none';
    teacherFields.style.display = 'none';
    
    // Show based on selected role
    if (this.value === 'student') {
        studentFields.style.display = 'block';
        // Make student fields required
        document.getElementById('student_id').required = true;
        document.getElementById('course').required = true;
        document.getElementById('year_level').required = true;
        // Remove teacher field requirements
        document.getElementById('teacher_id').required = false;
    } else if (this.value === 'teacher') {
        teacherFields.style.display = 'block';
        // Make teacher fields required
        document.getElementById('teacher_id').required = true;
        // Remove student field requirements
        document.getElementById('student_id').required = false;
        document.getElementById('course').required = false;
        document.getElementById('year_level').required = false;
    }
});

// Trigger change on page load if role is pre-selected
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    if (roleSelect.value) {
        roleSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection