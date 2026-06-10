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
            {{-- Statistics Summary (unchanged, already responsive) --}}
            <div class="row mb-4">
                <div class="col-md-4 col-6 mb-3">
                    <div class="card text-white bg-primary h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title">Total Hours</h5>
                            <h2 class="mb-0">{{ number_format($totalHours, 1) }}</h2>
                            <small>hours completed</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-6 mb-3">
                    <div class="card text-white bg-success h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title">Total Days</h5>
                            <h2 class="mb-0">{{ $totalDays }}</h2>
                            <small>days present</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-12 mb-3">
                    <div class="card text-white bg-info h-100">
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

            {{-- Mobile-friendly list of attendance records --}}
            @forelse($attendances as $attendance)
                <div class="card mb-3 shadow-sm">
                    <div class="card-body p-3">
                        {{-- Date and Session header --}}
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong class="fs-6">{{ \Carbon\Carbon::parse($attendance->date)->format('F d, Y') }}</strong>
                                <br>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($attendance->date)->format('l') }}</small>
                            </div>
                            <div>
                                @if(($attendance->session ?? '') === 'AM')
                                    <span class="badge bg-warning text-dark">AM</span>
                                @elseif(($attendance->session ?? '') === 'OT')
                                    <span class="badge bg-danger">OT</span>
                                @else
                                    <span class="badge bg-primary">PM</span>
                                @endif
                            </div>
                        </div>

                        {{-- Time In / Out / Hours --}}
                        <div class="row g-2 text-center mb-2">
                            <div class="col-4">
                                <div class="small text-muted">Time In</div>
                                <div class="fw-bold">{{ \Carbon\Carbon::parse($attendance->time_in)->format('h:i A') }}</div>
                            </div>
                            <div class="col-4">
                                <div class="small text-muted">Time Out</div>
                                <div class="fw-bold">
                                    @if($attendance->time_out)
                                        {{ \Carbon\Carbon::parse($attendance->time_out)->format('h:i A') }}
                                    @else
                                        <span class="badge bg-warning text-dark">Still working</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="small text-muted">Hours</div>
                                <div class="fw-bold text-success">{{ number_format($attendance->hours_worked, 2) }} hrs</div>
                            </div>
                        </div>

                        {{-- Status and Subject --}}
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <div>
                                @if($attendance->status == 'present')
                                    <span class="badge bg-success">Present</span>
                                @elseif($attendance->status == 'late')
                                    <span class="badge bg-warning text-dark">Late</span>
                                @elseif($attendance->status == 'half_day')
                                    <span class="badge bg-info">Half Day</span>
                                @else
                                    <span class="badge bg-danger">Absent</span>
                                @endif
                            </div>
                            <div class="text-muted small">
                                @if($attendance->internship && $attendance->internship->subject)
                                    {{ $attendance->internship->subject->code }}
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-5">
                    <i class="fas fa-clock fa-3x mb-3 d-block"></i>
                    <p>No attendance records found.</p>
                    <p class="small">Start by scanning your QR code to time in.</p>
                </div>
            @endforelse

            {{-- Pagination --}}
            <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap">
                <div class="small text-muted">
                    Showing {{ $attendances->firstItem() ?? 0 }} to {{ $attendances->lastItem() ?? 0 }} 
                    of {{ $attendances->total() ?? 0 }} records
                </div>
                <div>
                    {{ $attendances->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="mt-3 d-flex gap-2">
        <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Print Report
        </button>
    </div>
</div>
@endsection