{{-- resources/views/teacher/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- Welcome Card -->
            <div class="card mb-4">
                <div class="card-header" style="background-color: #216699; color: white;">
                    <h3 class="mb-0">Teacher Dashboard</h3>
                </div>
                <div class="card-body">
                    <p>Welcome back, <strong>{{ Auth::user()->name }}</strong>!</p>
                    <p><strong>Teacher ID:</strong> {{ Auth::user()->teacher_id }}</p>
                    <p><strong>Department:</strong> {{ Auth::user()->department }}</p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-header">My Students</div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $totalStudents ?? 0 }}</h5>
                            <p class="card-text">Students under supervision</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-header">Active Internships</div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $activeInternships ?? 0 }}</h5>
                            <p class="card-text">Current internship programs</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-info mb-3">
                        <div class="card-header">Today's Attendance</div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $todayAttendance ?? 0 }}</h5>
                            <p class="card-text">Students logged in today</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Students Section with Add Button -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #216699; color: white;">
                    <h4 class="mb-0"><i class="fas fa-users"></i> My Students</h4>
                    <a href="{{ route('teacher.students.create') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-user-plus"></i> Add New Student
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(isset($students) && count($students) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Course</th>
                                        <th>Year Level</th>
                                        <th>Subject</th>
                                        <th>Progress</th>
                                        <th>Total Hours</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $student)
                                    <tr>
                                        <td>{{ $student->student_id ?? 'N/A' }}</p>
                                        <td>{{ $student->name }}</p>
                                        <td>{{ $student->course ?? 'N/A' }}</p>
                                        <td>{{ $student->year_level ?? 'N/A' }}</p>
                                        <td>
                                            @if($student->internships && $student->internships->first())  <!-- Changed from studentEnrollments -->
                                                {{ $student->internships->first()->subject->code ?? 'N/A' }}
                                            @else
                                                <span class="badge bg-secondary">Not Assigned</span>  <!-- Changed text -->
                                            @endif
                                        </p>
                                        <td>
                                            @if($student->internships && $student->internships->first())  <!-- Changed from studentEnrollments -->
                                                @php
                                                    $progress = $student->internships->first()->progress ?? 0;
                                                @endphp
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-success" style="width: {{ $progress }}%;">
                                                        {{ number_format($progress, 1) }}%
                                                    </div>
                                                </div>
                                            @else
                                                <span class="badge bg-secondary">No Internship</span>
                                            @endif
                                        </p>
                                        <td>
                                            @php
                                                $totalHours = $student->attendances->sum('hours_worked') ?? 0;
                                            @endphp
                                            {{ number_format($totalHours, 1) }} hrs
                                        </p>
                                        <td>
                                            <a href="{{ route('teacher.students.show', $student) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                          </p>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-3">No students assigned to you yet.</p>
                            <a href="{{ route('teacher.students.create') }}" class="btn btn-primary">
                                <i class="fas fa-user-plus"></i> Add Your First Student
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-refresh attendance data every 30 seconds (optional)
    // Commented out by default - uncomment if needed
    /*
    setInterval(function() {
        location.reload();
    }, 30000);
    */
</script>
@endpush