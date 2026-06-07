@extends('layouts.app')

@section('title', 'My Attendance History')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header" style="background-color: #216699; color: white;">
            <h4 class="mb-0">
                <i class="fas fa-history"></i> My Attendance History
            </h4>
        </div>

        <div class="card-body">
            <!-- Statistics Summary -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-white bg-primary">
                        <div class="card-body text-center">
                            <h5 class="card-title">Total Hours</h5>
                            <h2 class="mb-0">{{ number_format($totalHours, 1) }}</h2>
                            <small>hours completed</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-success">
                        <div class="card-body text-center">
                            <h5 class="card-title">Total Days</h5>
                            <h2 class="mb-0">{{ $totalDays }}</h2>
                            <small>days present</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-info">
                        <div class="card-body text-center">
                            <h5 class="card-title">Average Hours/Day</h5>
                            <h2 class="mb-0">
                                {{ $totalDays > 0 ? number_format($totalHours / $totalDays, 1) : 0 }}
                            </h2>
                            <small>hours per day</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Records Table -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Hours Worked</th>
                            <th>Status</th>
                            <th>Subject</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                        <tr>
                            <td>
                                <strong>{{ \Carbon\Carbon::parse($attendance->date)->format('F d, Y') }}</strong>
                                <br>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($attendance->date)->format('l') }}</small>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($attendance->time_in)->format('h:i A') }}</td>
                            <td>
                                @if($attendance->time_out)
                                    {{ \Carbon\Carbon::parse($attendance->time_out)->format('h:i A') }}
                                @else
                                    <span class="badge bg-warning">Still working</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ number_format($attendance->hours_worked, 2) }}</strong> hrs
                            </td>
                            <td>
                                @if($attendance->status == 'present')
                                    <span class="badge bg-success">Present</span>
                                @elseif($attendance->status == 'late')
                                    <span class="badge bg-warning">Late</span>
                                @elseif($attendance->status == 'half_day')
                                    <span class="badge bg-info">Half Day</span>
                                @else
                                    <span class="badge bg-danger">Absent</span>
                                @endif
                            </td>
                            <td>
                                @if($attendance->internship && $attendance->internship->subject)
                                    {{ $attendance->internship->subject->code }}
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="fas fa-clock fa-3x mb-3"></i>
                                <p>No attendance records found.</p>
                                <p class="small">Start by scanning your QR code to time in.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3 d-flex justify-content-between align-items-center">
                <div>
                    Showing {{ $attendances->firstItem() ?? 0 }} to {{ $attendances->lastItem() ?? 0 }} 
                    of {{ $attendances->total() ?? 0 }} records
                </div>
                <div>
                    {{ $attendances->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-3">
        <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Print Report
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Optional: Add chart for attendance trends
    // You can add Chart.js integration here if needed
</script>
@endpush