@extends('layouts.app')

@section('title', 'Attendance Monitoring')

@section('content')
<div class="container">
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body text-center">
                    <h3>{{ $todayAttendance ?? 0 }}</h3>
                    <small>Total Today</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body text-center">
                    <h3>{{ $studentsClockedIn ?? 0 }}</h3>
                    <small>Currently Clocked In</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info">
                <div class="card-body text-center">
                    <h3>{{ $attendances->total() ?? 0 }}</h3>
                    <small>Total Records</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header" style="background-color: #216699; color: white;">
            <h4 class="mb-0">
                <i class="fas fa-clock"></i> Attendance Records
            </h4>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Student</th>
                            <th>Subject</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Hours</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($attendance->date)->format('M d, Y') }}</p>
                            <td>
                                {{ $attendance->student->name }}<br>
                                <small class="text-muted">{{ $attendance->student->student_id }}</small>
                            </p>
                            <td>{{ $attendance->internship->subject->code ?? 'N/A' }}</p>
                            <td>{{ \Carbon\Carbon::parse($attendance->time_in)->format('h:i A') }}</p>
                            <td>
                                @if($attendance->time_out)
                                    {{ \Carbon\Carbon::parse($attendance->time_out)->format('h:i A') }}
                                @else
                                    <span class="badge bg-warning">Working</span>
                                @endif
                            </p>
                            <td>{{ number_format($attendance->hours_worked, 2) }} hrs</p>
                            <td>
                                <span class="badge bg-{{ $attendance->status == 'present' ? 'success' : ($attendance->status == 'late' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($attendance->status) }}
                                </span>
                            </p>
                            <td>
                                <a href="{{ route('teacher.attendance.show', $attendance) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </p>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                                    <p>No attendance records found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </div>
            </div>

            <div class="mt-3">
                {{ $attendances->links() }}
            </div>
        </div>
    </div>
</div>
@endsection