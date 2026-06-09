{{-- resources/views/student/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<div class="container">
    @if(isset($noInternship) && $noInternship)
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> {{ $message }}
        </div>
    @else
        <div class="row justify-content-center">
            <div class="col-md-12">
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card text-white bg-primary">
                            <div class="card-header">Welcome</div>
                            <div class="card-body">
                                <h5 class="card-title">{{ Auth::user()->name }}</h5>
                                <p class="card-text">Student ID: {{ Auth::user()->student_id }}</p>
                                <p class="card-text">Course: {{ Auth::user()->course }} - Year {{ Auth::user()->year_level }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <div class="card text-white bg-success">
                            <div class="card-header">Progress</div>
                            <div class="card-body">
                                <h5 class="card-title">{{ number_format($progress, 1) }}%</h5>
                                <div class="progress bg-white-50">
                                    <div class="progress-bar" style="width: {{ $progress }}%"></div>
                                </div>
                                <p class="mt-2">{{ number_format($totalHours, 1) }} / {{ $requiredHours }} hours</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <div class="card text-white bg-info">
                            <div class="card-header">Internship Hours</div>
                            <div class="card-body">
                                <h5 class="card-title">Total Hours</h5>
                                <p class="card-text">Completed: {{ number_format($totalHours, 1) }} hours</p>
                                <p class="card-text">Remaining: {{ number_format($remainingHours, 1) }} hours</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card text-white bg-warning">
                            <div class="card-header">Subject</div>
                            <div class="card-body">
                                <h5 class="card-title">{{ $internship->subject->code }}</h5>
                                <p class="card-text">{{ $internship->subject->name }}</p>
                                <p class="card-text">Days Rendered: {{ $totalDays }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Scan QR Code Button -->
                <div class="mb-3">
                    <a href="{{ route('student.scan') }}" class="btn btn-primary">
                        <i class="fas fa-qrcode"></i> Scan QR Code
                    </a>
                </div>

                <!-- Today's Attendance (AM + PM + OT) -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5><i class="fas fa-clock"></i> Today's Attendance</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Session</th>
                                            <th>Time In</th>
                                            <th>Time Out</th>
                                            <th>Hours Worked</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- AM Row --}}
                                        <tr>
                                            <td><span class="badge bg-warning text-dark">AM</span></td>
                                            @if($todayAM)
                                                <td>{{ \Carbon\Carbon::parse($todayAM->time_in)->format('h:i A') }}</td>
                                                <td>{{ $todayAM->time_out ? \Carbon\Carbon::parse($todayAM->time_out)->format('h:i A') : '—' }}</td>
                                                <td><strong>{{ number_format($todayAM->hours_worked, 2) }}</strong> hrs</p>
                                                <td>
                                                    <span class="badge bg-{{ $todayAM->status == 'present' ? 'success' : ($todayAM->status == 'late' ? 'warning' : 'secondary') }}">
                                                        {{ ucfirst($todayAM->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($todayAM->time_in && !$todayAM->time_out)
                                                        <button class="btn btn-sm btn-danger time-out-btn" data-session="AM">
                                                            <i class="fas fa-sign-out-alt"></i> Time Out
                                                        </button>
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                            @else
                                                <td colspan="5" class="text-muted">Not yet recorded</td>
                                                <td>—</td>
                                            @endif
                                        </tr>
                                        {{-- PM Row --}}
                                        <tr>
                                            <td><span class="badge bg-primary">PM</span></td>
                                            @if($todayPM)
                                                <td>{{ \Carbon\Carbon::parse($todayPM->time_in)->format('h:i A') }}</td>
                                                <td>{{ $todayPM->time_out ? \Carbon\Carbon::parse($todayPM->time_out)->format('h:i A') : '—' }}</td>
                                                <td><strong>{{ number_format($todayPM->hours_worked, 2) }}</strong> hrs</p>
                                                <td>
                                                    <span class="badge bg-{{ $todayPM->status == 'present' ? 'success' : ($todayPM->status == 'late' ? 'warning' : 'secondary') }}">
                                                        {{ ucfirst($todayPM->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($todayPM->time_in && !$todayPM->time_out)
                                                        <button class="btn btn-sm btn-danger time-out-btn" data-session="PM">
                                                            <i class="fas fa-sign-out-alt"></i> Time Out
                                                        </button>
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                            @else
                                                <td colspan="5" class="text-muted">Not yet recorded</td>
                                                <td>—</td>
                                            @endif
                                        </tr>
                                        {{-- OT Row --}}
                                        <tr>
                                            <td><span class="badge bg-danger">OT</span></p>
                                            @if($todayOT ?? false)
                                                <td>{{ \Carbon\Carbon::parse($todayOT->time_in)->format('h:i A') }}</p>
                                                <td>{{ $todayOT->time_out ? \Carbon\Carbon::parse($todayOT->time_out)->format('h:i A') : '—' }}</p>
                                                <td><strong>{{ number_format($todayOT->hours_worked, 2) }}</strong> hrs</p>
                                                <td>
                                                    <span class="badge bg-{{ $todayOT->status == 'present' ? 'success' : ($todayOT->status == 'late' ? 'warning' : 'secondary') }}">
                                                        {{ ucfirst($todayOT->status) }}
                                                    </span>
                                                </p>
                                                <td>
                                                    @if($todayOT->time_in && !$todayOT->time_out)
                                                        <button class="btn btn-sm btn-danger time-out-btn" data-session="OT">
                                                            <i class="fas fa-sign-out-alt"></i> Time Out
                                                        </button>
                                                    @else
                                                        —
                                                    @endif
                                                </p>
                                            @else
                                                <td colspan="5" class="text-muted">Not yet recorded</td>
                                                <td>—</td>
                                            @endif
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="alert alert-info mt-3 mb-0 small">
                                <i class="fas fa-info-circle"></i> Use the <strong>Scan QR Code</strong> button to time in. Click <strong>Time Out</strong> when you finish a session.
                            </div>
                        </div>
                    </div>

                <!-- Recent Attendance Records (with Session column) -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-history"></i> Recent Attendance Records</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Session</th>
                                        <th>Time In</th>
                                        <th>Time Out</th>
                                        <th>Hours</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentAttendance as $attendance)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($attendance->date)->format('F d, Y') }}<br>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($attendance->date)->format('l') }}</small>
                                        </td>
                                        <td class="text-center">
                                            @if(($attendance->session ?? '') === 'AM')
                                                <span class="badge bg-warning text-dark">AM</span>
                                            @else
                                                <span class="badge bg-primary">PM</span>
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($attendance->time_in)->format('h:i A') }}</td>
                                        <td>{{ $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('h:i A') : 'Still working' }}</td>
                                        <td><strong>{{ number_format($attendance->hours_worked, 2) }}</strong> hrs</td>
                                        <td>
                                            <span class="badge bg-{{ $attendance->status == 'present' ? 'success' : ($attendance->status == 'late' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($attendance->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No attendance records yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.time-out-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const session = this.dataset.session;
        if (confirm(`Time out for ${session} session?`)) {
            fetch('{{ route("student.attendance.timeout") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ session: session })
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.success) location.reload();
            })
            .catch(err => alert('Error: ' + err.message));
        }
    });
});
</script>
@endpush