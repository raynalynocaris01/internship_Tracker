@extends('layouts.app')

@section('title', 'Add New User')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Add New User</h3>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role *</label>
                            <select class="form-control @error('role') is-invalid @enderror" 
                                    id="role" name="role" required onchange="toggleRoleFields()">
                                <option value="">Select Role</option>
                                <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                                <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>Teacher</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Student Fields -->
                        <div id="studentFields" style="display: none;">
                            <div class="mb-3">
                                <label for="student_id" class="form-label">Student ID *</label>
                                <input type="text" class="form-control @error('student_id') is-invalid @enderror" 
                                       id="student_id" name="student_id" value="{{ old('student_id') }}">
                                @error('student_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="course" class="form-label">Course *</label>
                                <select class="form-control @error('course') is-invalid @enderror" id="course" name="course">
                                    <option value="">Select Course</option>
                                    <option value="BSIT" {{ old('course') == 'BSIT' ? 'selected' : '' }}>BS Information Technology</option>
                                    <option value="BSCS" {{ old('course') == 'BSCS' ? 'selected' : '' }}>BS Computer Science</option>
                                    <option value="BSIS" {{ old('course') == 'BSIS' ? 'selected' : '' }}>BS Information Systems</option>
                                    <option value="BSECE" {{ old('course') == 'BSECE' ? 'selected' : '' }}>BS Electronics Engineering</option>
                                </select>
                                @error('course')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="year_level" class="form-label">Year Level *</label>
                                <select class="form-control @error('year_level') is-invalid @enderror" id="year_level" name="year_level">
                                    <option value="">Select Year</option>
                                    <option value="1" {{ old('year_level') == 1 ? 'selected' : '' }}>1st Year</option>
                                    <option value="2" {{ old('year_level') == 2 ? 'selected' : '' }}>2nd Year</option>
                                    <option value="3" {{ old('year_level') == 3 ? 'selected' : '' }}>3rd Year</option>
                                    <option value="4" {{ old('year_level') == 4 ? 'selected' : '' }}>4th Year</option>
                                </select>
                                @error('year_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Teacher Fields -->
                        <div id="teacherFields" style="display: none;">
                            <div class="mb-3">
                                <label for="teacher_id" class="form-label">Teacher ID *</label>
                                <input type="text" class="form-control @error('teacher_id') is-invalid @enderror" 
                                       id="teacher_id" name="teacher_id" value="{{ old('teacher_id') }}">
                                @error('teacher_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Common Fields -->
                        <div class="mb-3">
                            <label for="department" class="form-label">Department</label>
                            <input type="text" class="form-control @error('department') is-invalid @enderror" 
                                   id="department" name="department" value="{{ old('department') }}">
                            @error('department')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password *</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required>
                            <small class="text-muted">Minimum 8 characters</small>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password *</label>
                            <input type="password" class="form-control" 
                                   id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleRoleFields() {
    const role = document.getElementById('role').value;
    const studentFields = document.getElementById('studentFields');
    const teacherFields = document.getElementById('teacherFields');
    
    studentFields.style.display = 'none';
    teacherFields.style.display = 'none';
    
    if (role === 'student') {
        studentFields.style.display = 'block';
        document.getElementById('student_id').required = true;
        document.getElementById('course').required = true;
        document.getElementById('year_level').required = true;
    } else if (role === 'teacher') {
        teacherFields.style.display = 'block';
        document.getElementById('teacher_id').required = true;
    }
}

// Trigger on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleRoleFields();
});
</script>
@endsection