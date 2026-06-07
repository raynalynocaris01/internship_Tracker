@extends('layouts.app')

@section('title', 'Student Attendance')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header" style="background-color: #216699; color: white;">
            <h4 class="mb-0">
                <i class="fas fa-user-graduate"></i> Attendance: {{ $student->name }}
            </h4>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body text-center">
                            <h3>{{ number_format($totalHours, 1) }}</h3>
                            <small>Total Hours</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body text-center">
                            <h3>{{ $totalDays }}</h3>
                            <small>Days Present</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body text-center">
                            <h3>{{ $totalLate }}</h3>
                            <small>Late</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info">
                        <div class="card-body text-center">
                            <h3>{{ $activeInternship ? 'Active' : 'No Internship' }}</h3>
                            <small>Status</small>
                        </div>
                    </div>
                </div>
            </div>

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
                                <span class="badge bg-{{ $attendance->status == 'present' ? 'success' : 'warning' }}">
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
                <a href="{{ route('admin.attendances.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
</div>
@endsection