@extends('layouts.app')

@section('title', 'My Students')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #216699; color: white;">
            <h4 class="mb-0">
                <i class="fas fa-users"></i> My Students
            </h4>
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
                                <th>Email</th>
                                <th>Course</th>
                                <th>Year Level</th>
                                <th>Total Hours</th>
                                <th>Progress</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                            <tr>
                                <td><strong>{{ $student->student_id ?? 'N/A' }}</strong></td>
                                <td>{{ $student->name }}</td>
                                <td>{{ $student->email }}</td>
                                <td>{{ $student->course ?? 'N/A' }}</td>
                                <td>{{ $student->year_level ?? 'N/A' }} Year</p>
                                <td>
                                    @php
                                        $totalHours = $student->attendances->sum('hours_worked') ?? 0;
                                    @endphp
                                    {{ number_format($totalHours, 1) }} hrs
                                </td>
                                <td>
                                    @php
                                        $internship = $student->internships->first();  // Changed from studentEnrollments
                                        $progress = $internship ? $internship->progress : 0;
                                    @endphp
                                    <div class="progress" style="height: 25px;">
                                        <div class="progress-bar bg-success" style="width: {{ $progress }}%">
                                            {{ number_format($progress, 1) }}%
                                        </div>
                                    </div>
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
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5>No Students Found</h5>
                    <p class="text-muted">You haven't added any students yet.</p>
                    <a href="{{ route('teacher.students.create') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Add Your First Student
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection