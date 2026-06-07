{{-- resources/views/teacher/students/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Add New Student')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header" style="background-color: #216699; color: white;">
                    <h4 class="mb-0">
                        <i class="fas fa-user-plus"></i> Add New Student
                    </h4>
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

                    <form method="POST" action="{{ route('teacher.students.store') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">
                                Full Name <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <input id="name" type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name') }}" 
                                       placeholder="e.g., Juan Dela Cruz" required autofocus>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">
                                Email Address <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <input id="email" type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email') }}" 
                                       placeholder="e.g., juan.delacruz@student.edu" required>
                                <small class="text-muted">This will be used for login</small>
                                @error('email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="student_id" class="col-md-4 col-form-label text-md-end">
                                Student ID <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <input id="student_id" type="text" 
                                       class="form-control @error('student_id') is-invalid @enderror" 
                                       name="student_id" value="{{ old('student_id') }}" 
                                       placeholder="e.g., 2024-001" required>
                                <small class="text-muted">Official student ID number</small>
                                @error('student_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="course" class="col-md-4 col-form-label text-md-end">
                                Course <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <select id="course" class="form-control @error('course') is-invalid @enderror" 
                                        name="course" required>
                                    <option value="">Select Course</option>
                                    <option value="BSIT" {{ old('course') == 'BSIT' ? 'selected' : '' }}>
                                        BS Information Technology
                                    </option>
                                    <option value="BSCS" {{ old('course') == 'BSCS' ? 'selected' : '' }}>
                                        BS Computer Science
                                    </option>
                                    <option value="BSIS" {{ old('course') == 'BSIS' ? 'selected' : '' }}>
                                        BS Information Systems
                                    </option>
                                    <option value="BSECE" {{ old('course') == 'BSECE' ? 'selected' : '' }}>
                                        BS Electronics Engineering
                                    </option>
                                </select>
                                @error('course')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="year_level" class="col-md-4 col-form-label text-md-end">
                                Year Level <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <select id="year_level" class="form-control @error('year_level') is-invalid @enderror" 
                                        name="year_level" required>
                                    <option value="">Select Year Level</option>
                                    <option value="1" {{ old('year_level') == 1 ? 'selected' : '' }}>
                                        1st Year
                                    </option>
                                    <option value="2" {{ old('year_level') == 2 ? 'selected' : '' }}>
                                        2nd Year
                                    </option>
                                    <option value="3" {{ old('year_level') == 3 ? 'selected' : '' }}>
                                        3rd Year
                                    </option>
                                    <option value="4" {{ old('year_level') == 4 ? 'selected' : '' }}>
                                        4th Year
                                    </option>
                                </select>
                                @error('year_level')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="department" class="col-md-4 col-form-label text-md-end">
                                Department
                            </label>
                            <div class="col-md-6">
                                <select id="department" class="form-control @error('department') is-invalid @enderror" 
                                        name="department">
                                    <option value="">Select Department</option>
                                    <option value="Computer Science" {{ old('department') == 'Computer Science' ? 'selected' : '' }}>
                                        Computer Science
                                    </option>
                                    <option value="Information Technology" {{ old('department') == 'Information Technology' ? 'selected' : '' }}>
                                        Information Technology
                                    </option>
                                    <option value="Engineering" {{ old('department') == 'Engineering' ? 'selected' : '' }}>
                                        Engineering
                                    </option>
                                    <option value="Business" {{ old('department') == 'Business' ? 'selected' : '' }}>
                                        Business
                                    </option>
                                </select>
                                @error('department')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">
                                Password <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <input id="password" type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       name="password" required>
                                <small class="text-muted">Minimum 8 characters</small>
                                @error('password')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password_confirmation" class="col-md-4 col-form-label text-md-end">
                                Confirm Password <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <input id="password_confirmation" type="password" 
                                       class="form-control" 
                                       name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary" style="background-color: #216699; border-color: #216699;">
                                    <i class="fas fa-save"></i> Create Student Account
                                </button>
                                <a href="{{ route('teacher.students.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Information Card -->
            <div class="card mt-3">
                <div class="card-body">
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Note:</strong> After creating the student account:
                        <ul class="mb-0 mt-2">
                            <li>The student will receive a unique QR code automatically</li>
                            <li>The student can login using their email and the password you set</li>
                            <li>The student's QR code will be available in their dashboard</li>
                            <li>You need to enroll the student to a subject after account creation</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Optional: Add password strength indicator
    document.getElementById('password')?.addEventListener('input', function() {
        const password = this.value;
        const strengthIndicator = document.getElementById('passwordStrength');
        
        if (!strengthIndicator) return;
        
        if (password.length === 0) {
            strengthIndicator.innerHTML = '';
            strengthIndicator.className = '';
        } else if (password.length < 6) {
            strengthIndicator.innerHTML = 'Weak';
            strengthIndicator.className = 'text-danger';
        } else if (password.length < 8) {
            strengthIndicator.innerHTML = 'Medium';
            strengthIndicator.className = 'text-warning';
        } else {
            strengthIndicator.innerHTML = 'Strong';
            strengthIndicator.className = 'text-success';
        }
    });
</script>
@endpush