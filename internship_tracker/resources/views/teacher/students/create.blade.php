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

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           name="name" value="{{ old('name') }}" required>
                                    @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           name="email" value="{{ old('email') }}" required>
                                    @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="student_id" class="form-label">Student ID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('student_id') is-invalid @enderror" 
                                           name="student_id" value="{{ old('student_id') }}" required>
                                    @error('student_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="course" class="form-label">Course <span class="text-danger">*</span></label>
                                    <select class="form-control @error('course') is-invalid @enderror" name="course" required>
                                        <option value="">Select Course</option>
                                        <option value="BSIT">BS Information Technology</option>
                                        <option value="BSCS">BS Computer Science</option>
                                        <option value="BSIS">BS Information Systems</option>
                                        <option value="BSECE">BS Electronics Engineering</option>
                                    </select>
                                    @error('course')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="year_level" class="form-label">Year Level <span class="text-danger">*</span></label>
                                    <select class="form-control @error('year_level') is-invalid @enderror" name="year_level" required>
                                        <option value="">Select Year</option>
                                        <option value="1">1st Year</option>
                                        <option value="2">2nd Year</option>
                                        <option value="3">3rd Year</option>
                                        <option value="4">4th Year</option>
                                    </select>
                                    @error('year_level')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>

                        <!-- Internship Assignment Section -->
                        <div class="card bg-light mb-3">
                            <div class="card-header">
                                <strong><i class="fas fa-briefcase"></i> Internship Assignment</strong>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="subject_id" class="form-label">Subject <span class="text-danger">*</span></label>
                                            <select class="form-control @error('subject_id') is-invalid @enderror" 
                                                    name="subject_id" required>
                                                <option value="">Select Subject</option>
                                                @foreach($subjects as $subject)
                                                    <option value="{{ $subject->id }}">
                                                        {{ $subject->code }} - {{ $subject->name }} ({{ $subject->required_hours }} hrs)
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('subject_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="start_date" class="form-label">Start Date</label>
                                            <input type="date" class="form-control" name="start_date" 
                                                   value="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="company_name" class="form-label">Company Name</label>
                                            <input type="text" class="form-control" name="company_name" 
                                                   placeholder="Leave blank for school-based">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="position" class="form-label">Position</label>
                                            <input type="text" class="form-control" name="position" 
                                                   placeholder="e.g., Intern, Assistant">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           name="password" required>
                                    <small class="text-muted">Minimum 8 characters</small>
                                    @error('password')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="password_confirmation" required>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Add Student & Assign Internship
                        </button>
                        <a href="{{ route('teacher.students.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection