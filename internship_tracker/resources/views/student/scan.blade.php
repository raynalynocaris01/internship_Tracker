{{-- resources/views/student/scan.blade.php --}}
@extends('layouts.app')

@section('title', 'QR Attendance Scan')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow mt-4">

                @if($success && isset($action) && $action === 'timein')
                {{-- ✅ Time In Success --}}
                <div class="card-header bg-success text-white text-center py-3">
                    <h4 class="mb-0"><i class="fas fa-check-circle fa-lg"></i> Time In Recorded</h4>
                </div>
                <div class="card-body text-center py-4">
                    <i class="fas fa-sign-in-alt fa-4x text-success mb-3"></i>
                    <h5>{{ $student->name }}</h5>
                    <p class="text-muted mb-1">{{ $student->student_id }}</p>
                    <div class="alert alert-success mt-3">
                        <strong>{{ $message }}</strong>
                    </div>
                    <p class="text-muted small">
                        <i class="fas fa-book"></i> {{ $qrCode->subject->code }} — {{ $qrCode->subject->name }}<br>
                        <i class="fas fa-users"></i> {{ $qrCode->section->name }}
                    </p>
                    <p class="text-muted small mt-2">
                        Scan again when you're leaving to record your <strong>Time Out</strong>.
                    </p>
                </div>

                @elseif($success && isset($action) && $action === 'timeout')
                {{-- ✅ Time Out Success --}}
                <div class="card-header text-white text-center py-3" style="background:#e6a800;">
                    <h4 class="mb-0"><i class="fas fa-check-circle fa-lg"></i> Time Out Recorded</h4>
                </div>
                <div class="card-body text-center py-4">
                    <i class="fas fa-sign-out-alt fa-4x text-warning mb-3"></i>
                    <h5>{{ $student->name }}</h5>
                    <p class="text-muted mb-1">{{ $student->student_id }}</p>
                    <div class="alert alert-warning mt-3">
                        <strong>{{ $message }}</strong>
                    </div>
                    <p class="text-muted small">
                        <i class="fas fa-book"></i> {{ $qrCode->subject->code }} — {{ $qrCode->subject->name }}<br>
                        <i class="fas fa-users"></i> {{ $qrCode->section->name }}
                    </p>
                    @if(isset($attendance) && $attendance->hours_worked)
                        <div class="badge bg-info fs-6 mt-2">
                            {{ number_format($attendance->hours_worked, 2) }} hours worked
                        </div>
                    @endif
                </div>

                @elseif($success && isset($alreadyDone))
                {{-- ℹ️ Already Complete --}}
                <div class="card-header bg-info text-white text-center py-3">
                    <h4 class="mb-0"><i class="fas fa-info-circle"></i> Already Recorded</h4>
                </div>
                <div class="card-body text-center py-4">
                    <i class="fas fa-calendar-check fa-4x text-info mb-3"></i>
                    <h5>{{ $student->name }}</h5>
                    <div class="alert alert-info mt-3">{{ $message }}</div>
                    @if(isset($attendance))
                    <p class="text-muted small">
                        In: <strong>{{ \Carbon\Carbon::parse($attendance->time_in)->format('h:i A') }}</strong>
                        &nbsp;|&nbsp;
                        Out: <strong>{{ \Carbon\Carbon::parse($attendance->time_out)->format('h:i A') }}</strong>
                    </p>
                    @endif
                </div>

                @elseif(isset($notEnrolled))
                {{-- ❌ Not Enrolled --}}
                <div class="card-header bg-danger text-white text-center py-3">
                    <h4 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Not Registered</h4>
                </div>
                <div class="card-body text-center py-4">
                    <i class="fas fa-user-times fa-4x text-danger mb-3"></i>
                    <h5>{{ $student->name }}</h5>
                    <p class="text-muted mb-1">{{ $student->student_id }}</p>
                    <div class="alert alert-danger mt-3">
                        <strong>{{ $error }}</strong>
                    </div>
                    <p class="text-muted small">
                        Please ask your teacher to add you to this subject/section first.
                    </p>
                </div>

                @else
                {{-- ❌ Invalid / Expired QR --}}
                <div class="card-header bg-secondary text-white text-center py-3">
                    <h4 class="mb-0"><i class="fas fa-ban"></i> Invalid QR Code</h4>
                </div>
                <div class="card-body text-center py-4">
                    <i class="fas fa-qrcode fa-4x text-secondary mb-3"></i>
                    <div class="alert alert-secondary mt-3">
                        <strong>{{ $error ?? 'This QR code is no longer valid.' }}</strong>
                    </div>
                    <p class="text-muted small">Ask your teacher to generate a new QR code.</p>
                </div>
                @endif

                <div class="card-footer text-center">
                    <a href="{{ route('student.dashboard') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-home"></i> Back to Dashboard
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection