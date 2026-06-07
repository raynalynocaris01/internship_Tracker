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
                                <h5 class="card-title">{{ $internship->subject->code }}</h5>  <!-- Changed from $enrollment -->
                                <p class="card-text">{{ $internship->subject->name }}</p>  <!-- Changed from $enrollment -->
                                <p class="card-text">Days Rendered: {{ $totalDays }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- QR Code and Time Tracking -->
                <div class="row">
                    <div class="col-md-5 mb-4">
                        <div class="card text-center">
                            <div class="card-header">
                                <h5><i class="fas fa-qrcode"></i> My QR Code</h5>
                            </div>
                            <div class="card-body">
                                <div class="qr-container mb-3 text-center">
                                    {!! $qrCodeImage !!}
                                </div>
                                <p class="text-muted">Scan this QR code to time in/out</p>
                                <p class="text-muted small">QR Code Value: <strong>{{ $qrCode->qr_code }}</strong></p>
                                <button class="btn btn-sm btn-secondary" onclick="window.print()">
                                    <i class="fas fa-print"></i> Print QR Code
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-7 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-clock"></i> Today's Attendance</h5>
                            </div>
                            <div class="card-body text-center">
                                @if($todayAttendance)
                                    @if($todayAttendance->time_in && !$todayAttendance->time_out)
                                        <div class="alert alert-info">
                                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                                            <h5>You are currently TIMED IN</h5>
                                            <p>Time in: {{ \Carbon\Carbon::parse($todayAttendance->time_in)->format('h:i A') }}</p>
                                            <button class="btn btn-danger btn-lg" id="timeOutBtn">
                                                <i class="fas fa-sign-out-alt"></i> Time Out
                                            </button>
                                        </div>
                                    @elseif($todayAttendance->time_out)
                                        <div class="alert alert-success">
                                            <i class="fas fa-check-double fa-2x mb-2"></i>
                                            <h5>Attendance Completed for Today</h5>
                                            <p>Time in: {{ \Carbon\Carbon::parse($todayAttendance->time_in)->format('h:i A') }}</p>
                                            <p>Time out: {{ \Carbon\Carbon::parse($todayAttendance->time_out)->format('h:i A') }}</p>
                                            <p>Hours worked: <strong>{{ number_format($todayAttendance->hours_worked, 2) }}</strong></p>
                                        </div>
                                    @endif
                                @else
                                    <div class="alert alert-secondary">
                                        <i class="fas fa-clock fa-2x mb-2"></i>
                                        <h5>Not yet timed in today</h5>
                                        <p>Click the button below to time in</p>
                                        <button class="btn btn-success btn-lg" id="timeInBtn">
                                            <i class="fas fa-sign-in-alt"></i> Time In
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Attendance Records -->
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
                                        <th>Time In</th>
                                        <th>Time Out</th>
                                        <th>Hours</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentAttendance as $attendance)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($attendance->date)->format('F d, Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($attendance->time_in)->format('h:i A') }}</td>
                                        <td>{{ $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('h:i A') : 'Still working' }}</td>
                                        <td>{{ number_format($attendance->hours_worked, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $attendance->status == 'present' ? 'success' : ($attendance->status == 'late' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($attendance->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No attendance records yet.</td>
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
function timeIn() {
    const qrData = prompt('Please scan your QR code or enter the code below:\n\nYour QR Code: {{ $qrCode->qr_code ?? "N/A" }}\n\nFor testing, you can copy the QR code value above.');
    
    if (qrData) {
        // Show loading indicator
        const btn = document.getElementById('timeInBtn');
        const originalText = btn?.innerHTML;
        if (btn) {
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            btn.disabled = true;
        }
        
        fetch('{{ route("student.attendance.timein") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({qr_data: qrData})
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if(data.success) location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        })
        .finally(() => {
            if (btn) {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        });
    }
}

function timeOut() {
    if(confirm('Are you sure you want to time out?')) {
        const btn = document.getElementById('timeOutBtn');
        const originalText = btn?.innerHTML;
        if (btn) {
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            btn.disabled = true;
        }
        
        fetch('{{ route("student.attendance.timeout") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if(data.success) location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        })
        .finally(() => {
            if (btn) {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        });
    }
}

// Add event listeners when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    const timeInBtn = document.getElementById('timeInBtn');
    const timeOutBtn = document.getElementById('timeOutBtn');
    
    if (timeInBtn) {
        timeInBtn.addEventListener('click', timeIn);
    }
    if (timeOutBtn) {
        timeOutBtn.addEventListener('click', timeOut);
    }
});
</script>
@endpush