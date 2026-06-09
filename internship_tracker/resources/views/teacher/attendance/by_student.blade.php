@extends('layouts.app')

@section('title', 'Student Attendance')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header" style="background-color: #216699; color: white;">
            <h4 class="mb-0">
                <i class="fas fa-clock"></i> Attendance History
                @if(isset($attendances) && $attendances->first() && $attendances->first()->student)
                    : {{ $attendances->first()->student->name }}
                @endif
            </h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Session</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Hours Worked</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($attendance->date)->format('F d, Y') }}<br>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($attendance->date)->format('l') }}</small>
                            </td>
                            <td class="text-center">
                                @if($attendance->session == 'AM')
                                    <span class="badge bg-warning text-dark"><i class="fas fa-sun"></i> AM</span>
                                @elseif($attendance->session == 'PM')
                                    <span class="badge bg-primary"><i class="fas fa-moon"></i> PM</span>
                                @elseif($attendance->session == 'OT')
                                    <span class="badge bg-danger"><i class="fas fa-clock"></i> OT</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($attendance->time_in)->format('h:i A') }}</td>
                            <td>
                                @if($attendance->time_out)
                                    {{ \Carbon\Carbon::parse($attendance->time_out)->format('h:i A') }}
                                @else
                                    <span class="badge bg-warning text-dark">Working</span>
                                @endif
                            </td>
                            <td><strong>{{ number_format($attendance->hours_worked, 2) }}</strong> hrs</p>
                            <td>
                                <span class="badge bg-{{ $attendance->status == 'present' ? 'success' : ($attendance->status == 'late' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($attendance->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-clock fa-3x mb-3 d-block"></i>
                                    No attendance records found for this student.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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