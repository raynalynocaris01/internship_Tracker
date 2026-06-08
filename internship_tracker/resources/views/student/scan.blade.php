@extends('layouts.app')

@section('title', 'QR Scan Result')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">

                {{-- Header & body based on result type --}}
                @if($success && isset($action) && $action === 'timein')
                {{-- ✅ Time In Success --}}
                <div class="card-header bg-success text-white text-center py-4 border-0">
                    <h3 class="mb-0"><i class="fas fa-check-circle me-2"></i> Time In Recorded</h3>
                </div>
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-sign-in-alt fa-5x text-success opacity-75"></i>
                    </div>
                    <h4 class="fw-bold mb-2">{{ $student->name }}</h4>
                    <p class="text-muted mb-4">{{ $student->student_id }}</p>
                    <div class="alert alert-success py-3">
                        <i class="fas fa-check-circle me-2"></i> <strong>{{ $message }}</strong>
                    </div>
                    <div class="bg-light rounded-3 p-3 mt-3">
                        <p class="mb-1"><i class="fas fa-book me-2 text-primary"></i> {{ $qrCode->subject->code }} — {{ $qrCode->subject->name }}</p>
                        <p class="mb-0"><i class="fas fa-users me-2 text-info"></i> {{ $qrCode->section->name }}</p>
                    </div>
                    <p class="text-muted mt-4 mb-0 small">
                        <i class="fas fa-info-circle"></i> Scan again when you leave to record your <strong>Time Out</strong>.
                    </p>
                </div>

                @elseif($success && isset($action) && $action === 'timeout')
                {{-- ✅ Time Out Success --}}
                <div class="card-header text-dark text-center py-4 border-0" style="background:#f0c674;">
                    <h3 class="mb-0"><i class="fas fa-check-circle me-2"></i> Time Out Recorded</h3>
                </div>
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-sign-out-alt fa-5x text-warning opacity-75"></i>
                    </div>
                    <h4 class="fw-bold mb-2">{{ $student->name }}</h4>
                    <p class="text-muted mb-4">{{ $student->student_id }}</p>
                    <div class="alert alert-warning py-3">
                        <i class="fas fa-check-circle me-2"></i> <strong>{{ $message }}</strong>
                    </div>
                    <div class="bg-light rounded-3 p-3 mt-3">
                        <p class="mb-1"><i class="fas fa-book me-2 text-primary"></i> {{ $qrCode->subject->code }} — {{ $qrCode->subject->name }}</p>
                        <p class="mb-0"><i class="fas fa-users me-2 text-info"></i> {{ $qrCode->section->name }}</p>
                    </div>
                    @if(isset($attendance) && $attendance->hours_worked)
                        <div class="mt-3">
                            <span class="badge bg-info fs-6 px-3 py-2">
                                <i class="fas fa-hourglass-half me-1"></i> {{ number_format($attendance->hours_worked, 2) }} hours worked
                            </span>
                        </div>
                    @endif
                </div>

                @elseif($success && isset($alreadyDone))
                {{-- ℹ️ Already Complete --}}
                <div class="card-header bg-info text-white text-center py-4 border-0">
                    <h3 class="mb-0"><i class="fas fa-info-circle me-2"></i> Already Recorded</h3>
                </div>
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-calendar-check fa-5x text-info opacity-75"></i>
                    </div>
                    <h4 class="fw-bold mb-2">{{ $student->name }}</h4>
                    <div class="alert alert-info py-3">
                        <i class="fas fa-info-circle me-2"></i> <strong>{{ $message }}</strong>
                    </div>
                    @if(isset($attendance))
                        <div class="bg-light rounded-3 p-3 mt-3">
                            <p class="mb-1"><i class="fas fa-hourglass-start me-2"></i> In: <strong>{{ \Carbon\Carbon::parse($attendance->time_in)->format('h:i A') }}</strong></p>
                            <p class="mb-0"><i class="fas fa-hourglass-end me-2"></i> Out: <strong>{{ \Carbon\Carbon::parse($attendance->time_out)->format('h:i A') }}</strong></p>
                        </div>
                    @endif
                </div>

                @elseif(isset($notEnrolled))
                {{-- ❌ Not Enrolled --}}
                <div class="card-header bg-danger text-white text-center py-4 border-0">
                    <h3 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i> Not Registered</h3>
                </div>
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-user-times fa-5x text-danger opacity-75"></i>
                    </div>
                    <h4 class="fw-bold mb-2">{{ $student->name }}</h4>
                    <p class="text-muted mb-3">{{ $student->student_id }}</p>
                    <div class="alert alert-danger py-3">
                        <i class="fas fa-exclamation-circle me-2"></i> <strong>{{ $error }}</strong>
                    </div>
                    <div class="bg-light rounded-3 p-3 mt-3">
                        <p class="mb-1"><i class="fas fa-book me-2 text-primary"></i> {{ $qrCode->subject->code }} — {{ $qrCode->subject->name }}</p>
                        <p class="mb-0"><i class="fas fa-users me-2 text-info"></i> {{ $qrCode->section->name }}</p>
                    </div>
                    <p class="text-muted mt-4 small">Please ask your teacher to add you to this subject/section.</p>
                </div>

                @else
                {{-- ❌ Invalid / Expired QR --}}
                <div class="card-header bg-secondary text-white text-center py-4 border-0">
                    <h3 class="mb-0"><i class="fas fa-ban me-2"></i> Invalid QR Code</h3>
                </div>
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-qrcode fa-5x text-secondary opacity-75"></i>
                    </div>
                    <div class="alert alert-secondary py-3">
                        <i class="fas fa-exclamation-triangle me-2"></i> <strong>{{ $error ?? 'This QR code is no longer valid.' }}</strong>
                    </div>
                    <p class="text-muted mt-3">Ask your teacher to generate a new QR code.</p>
                </div>
                @endif

                {{-- Footer with back button --}}
                <div class="card-footer text-center bg-white border-0 pb-4">
                    <a href="{{ route('student.dashboard') }}" class="btn btn-secondary px-4">
                        <i class="fas fa-home me-2"></i> Back to Dashboard
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection