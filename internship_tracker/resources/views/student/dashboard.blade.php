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

                {{-- Stats Cards --}}
                <div class="row mb-4">
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card text-white bg-primary h-100">
                            <div class="card-header py-2 small">Welcome</div>
                            <div class="card-body py-2">
                                <h6 class="card-title mb-1">{{ Auth::user()->name }}</h6>
                                <p class="card-text mb-0 small">ID: {{ Auth::user()->student_id }}</p>
                                <p class="card-text small">{{ Auth::user()->course }} - Yr {{ Auth::user()->year_level }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card text-white bg-success h-100">
                            <div class="card-header py-2 small">Progress</div>
                            <div class="card-body py-2">
                                <h5 class="card-title mb-1">{{ number_format($progress, 1) }}%</h5>
                                <div class="progress mb-2" style="height:6px;background:rgba(255,255,255,.3)">
                                    <div class="progress-bar bg-white" style="width:{{ $progress }}%"></div>
                                </div>
                                <p class="card-text small mb-0">{{ number_format($totalHours, 1) }} / {{ $requiredHours }} hrs</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card text-white bg-info h-100">
                            <div class="card-header py-2 small">Hours</div>
                            <div class="card-body py-2">
                                <p class="small mb-1">Done: <strong>{{ number_format($totalHours, 1) }}</strong> hrs</p>
                                <p class="small mb-0">Left: <strong>{{ number_format($remainingHours, 1) }}</strong> hrs</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card text-white bg-warning h-100">
                            <div class="card-header py-2 small">Subject</div>
                            <div class="card-body py-2">
                                <h6 class="card-title mb-1">{{ $internship->subject->code }}</h6>
                                <p class="small mb-1">{{ $internship->subject->name }}</p>
                                <p class="small mb-0">Days: {{ $totalDays }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Scan QR Button --}}
                <div class="mb-3">
                    <a href="{{ route('student.scan') }}" class="btn btn-primary btn-lg w-100">
                        <i class="fas fa-qrcode me-2"></i> Scan QR Code to Attend
                    </a>
                </div>

                {{-- Today's Attendance — card-based layout (no horizontal scroll) --}}
                <div class="card mb-4">
                    <div class="card-header" style="background-color:#216699;color:white;">
                        <h5 class="mb-0">
                            <i class="fas fa-clock"></i> Today's Attendance
                            <small class="ms-2 opacity-75">{{ \Carbon\Carbon::today()->format('F d, Y') }}</small>
                        </h5>
                    </div>
                    <div class="card-body p-3">

                        {{-- AM Session Card --}}
                        @php
                            $sessions = [
                                ['key'=>'AM',  'record'=>$todayAM ?? null,  'label'=>'Morning',   'badgeClass'=>'bg-warning text-dark', 'btnClass'=>'btn-warning',  'icon'=>'fa-sun',   'bg'=>'#fffde7'],
                                ['key'=>'PM',  'record'=>$todayPM ?? null,  'label'=>'Afternoon', 'badgeClass'=>'bg-primary',           'btnClass'=>'btn-primary',  'icon'=>'fa-moon',  'bg'=>'#e3f2fd'],
                                ['key'=>'OT',  'record'=>$todayOT ?? null,  'label'=>'Overtime',  'badgeClass'=>'bg-danger',            'btnClass'=>'btn-danger',   'icon'=>'fa-clock', 'bg'=>'#fce4ec'],
                            ];
                        @endphp

                        @foreach($sessions as $sess)
                        @php $rec = $sess['record']; @endphp
                        <div class="rounded p-3 mb-3" style="background:{{ $sess['bg'] }};border:1px solid rgba(0,0,0,.06);">

                            {{-- Session header row --}}
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge {{ $sess['badgeClass'] }} fs-6 px-3 py-2">
                                        <i class="fas {{ $sess['icon'] }} me-1"></i>{{ $sess['key'] }}
                                    </span>
                                    <span class="fw-semibold text-muted small">{{ $sess['label'] }}</span>
                                </div>
                                @if($rec && $rec->time_in && !$rec->time_out)
                                    {{-- Time Out button visible right here, no scroll needed --}}
                                    <button class="btn {{ $sess['btnClass'] }} btn-sm time-out-btn fw-bold px-3"
                                            data-session="{{ $sess['key'] }}">
                                        <i class="fas fa-sign-out-alt me-1"></i> Time Out
                                    </button>
                                @elseif($rec && $rec->time_out)
                                    <span class="badge bg-success py-2 px-3">
                                        <i class="fas fa-check-circle me-1"></i> Done
                                    </span>
                                @endif
                            </div>

                            @if($rec)
                                {{-- Time In / Out / Hours row --}}
                                <div class="row g-2 text-center">
                                    <div class="col-4">
                                        <div class="small text-muted mb-1">Time In</div>
                                        <div class="fw-bold">
                                            {{ \Carbon\Carbon::parse($rec->time_in)->format('h:i A') }}
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="small text-muted mb-1">Time Out</div>
                                        <div class="fw-bold">
                                            {{ $rec->time_out ? \Carbon\Carbon::parse($rec->time_out)->format('h:i A') : '—' }}
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="small text-muted mb-1">Hours</div>
                                        <div class="fw-bold">
                                            {{ number_format($rec->hours_worked, 2) }} hrs
                                        </div>
                                    </div>
                                </div>
                                {{-- Status badge --}}
                                <div class="mt-2">
                                    <span class="badge bg-{{ $rec->status == 'present' ? 'success' : ($rec->status == 'late' ? 'warning text-dark' : 'secondary') }}">
                                        {{ ucfirst($rec->status) }}
                                    </span>
                                </div>
                            @else
                                <div class="text-muted small text-center py-1">
                                    <i class="fas fa-minus-circle me-1"></i> Not yet recorded
                                </div>
                            @endif

                        </div>
                        @endforeach

                        <div class="alert alert-info mb-0 small py-2">
                            <i class="fas fa-info-circle"></i>
                            Scan the teacher's QR code to time in. Tap <strong>Time Out</strong> when you finish a session.
                        </div>
                    </div>
                </div>

                {{-- Recent Attendance --}}
                <div class="card">
                    <div class="card-header" style="background-color:#216699;color:white;">
                        <h5 class="mb-0"><i class="fas fa-history"></i> Recent Attendance Records</h5>
                    </div>
                    <div class="card-body p-0">
                        @forelse($recentAttendance as $attendance)
                        <div class="d-flex align-items-start px-3 py-3 border-bottom">
                            {{-- Date --}}
                            <div class="me-3 text-center" style="min-width:48px;">
                                <div class="fw-bold" style="font-size:18px;line-height:1;">
                                    {{ \Carbon\Carbon::parse($attendance->date)->format('d') }}
                                </div>
                                <div class="text-muted small">
                                    {{ \Carbon\Carbon::parse($attendance->date)->format('M') }}
                                </div>
                            </div>
                            {{-- Info --}}
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    @if(($attendance->session ?? '') === 'AM')
                                        <span class="badge bg-warning text-dark">AM</span>
                                    @elseif(($attendance->session ?? '') === 'OT')
                                        <span class="badge bg-danger">OT</span>
                                    @else
                                        <span class="badge bg-primary">PM</span>
                                    @endif
                                    <span class="badge bg-{{ $attendance->status == 'present' ? 'success' : ($attendance->status == 'late' ? 'warning text-dark' : 'danger') }}">
                                        {{ ucfirst($attendance->status) }}
                                    </span>
                                </div>
                                <div class="small text-muted">
                                    In: <strong>{{ \Carbon\Carbon::parse($attendance->time_in)->format('h:i A') }}</strong>
                                    &nbsp;·&nbsp;
                                    Out: <strong>{{ $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('h:i A') : 'Working' }}</strong>
                                </div>
                            </div>
                            {{-- Hours --}}
                            <div class="text-end ms-2">
                                <div class="fw-bold text-success">{{ number_format($attendance->hours_worked, 2) }}</div>
                                <div class="text-muted small">hrs</div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-clock fa-2x mb-2 d-block"></i>
                            No attendance records yet.
                        </div>
                        @endforelse
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
    btn.addEventListener('click', function () {
        const session = this.dataset.session;
        if (!confirm(`Record Time Out for the ${session} session?`)) return;

        const self = this;
        self.disabled = true;
        self.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Recording…';

        fetch('{{ route("student.attendance.timeout") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ session: session })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Could not record time out.');
                self.disabled = false;
                self.innerHTML = '<i class="fas fa-sign-out-alt me-1"></i> Time Out';
            }
        })
        .catch(() => {
            alert('Network error. Please try again.');
            self.disabled = false;
            self.innerHTML = '<i class="fas fa-sign-out-alt me-1"></i> Time Out';
        });
    });
});
</script>
@endpush