@extends('layouts.app')

@section('title', 'Student Attendance')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header" style="background-color: #216699; color: white;">
            <h4 class="mb-0">
                <i class="fas fa-user-graduate"></i> Student Attendance Records
            </h4>
        </div>
        <div class="card-body">
            @if($attendances->count() > 0)
                @php
                    $student = $attendances->first()->student;
                @endphp
                <div class="alert alert-info">
                    <strong>Student:</strong> {{ $student->name }} ({{ $student->student_id }})<br>
                    <strong>Course:</strong> {{ $student->course }} - Year {{ $student->year_level }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Subject</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Hours</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($attendance->date)->format('M d, Y') }}</p>
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
                        </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No attendance records found.</p>
                            </tr>
                        @endforelse
                    </tbody>
                </div>
            </div>
            {{ $attendances->links() }}

            <div class="mt-3">
                <a href="{{ route('teacher.attendance.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>
</div>
@endsection