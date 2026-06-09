{{-- resources/views/teacher/qrcodes/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Subject QR Code – ' . $qrCode->subject->code)

@section('content')
<div class="container">

    <div class="row justify-content-center">
        <div class="col-lg-7">

            {{-- QR Card --}}
            <div class="card shadow">
                <div class="card-header text-white text-center py-3"
                     style="background-color: #216699;">
                    <h4 class="mb-0">
                        <i class="fas fa-qrcode"></i>
                        {{ $qrCode->subject->code }} — {{ $qrCode->section->name }}
                    </h4>
                    <small>
                        @if($qrCode->session === 'AM')
                            <i class="fas fa-sun text-warning"></i> Morning (AM) Session
                        @else
                            <i class="fas fa-moon"></i> Afternoon (PM) Session
                        @endif
                        &nbsp;|&nbsp; {{ $qrCode->valid_date->format('F d, Y') }}
                    </small>
                </div>
                @php
                    $expiresAt = match($qrCode->session) {
                        'AM' => \Carbon\Carbon::parse($qrCode->valid_date)->setTime(11, 50),
                        'PM' => \Carbon\Carbon::parse($qrCode->valid_date)->setTime(16, 50),
                        'OT' => \Carbon\Carbon::parse($qrCode->valid_date)->setTime(23, 59, 59),
                    };
                @endphp

                <div class="card-body text-center py-4">
                        {{-- QR image via free API (no GD needed) --}}
                        <div class="mb-3">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=280x280&data={{ urlencode($scanUrl) }}"
                                alt="QR Code"
                                class="img-fluid border rounded p-2 d-block mx-auto"
                                style="max-width:280px;">
                        </div>

                        <p class="text-muted small mb-1">
                            Students open their phone camera and scan this code.
                        </p>
                      <p class="text-muted small">
                        QR expires at <strong>{{ $expiresAt->format('h:i A') }}</strong>.
                        @if($qrCode->session != 'OT')
                            Students cannot time in after this time.
                        @endif
                    </p>

                        {{-- Close session --}}
                        <form method="POST" action="{{ route('teacher.qrcode.deactivate', $qrCode) }}" class="mt-3">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Close this QR session?')">
                                <i class="fas fa-times-circle"></i> Close QR Session
                            </button>
                        </form>
                    </div>
            </div>

            {{-- Live Scan Log --}}
            <div class="card mt-4 shadow-sm">
                <div class="card-header" style="background-color:#28a745; color:white;">
                    <h5 class="mb-0">
                        <i class="fas fa-list-check"></i> Scanned Students
                        <span class="badge bg-light text-dark ms-2" id="scan-count">0</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle" id="scan-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Student</th>
                                    <th>ID</th>
                                    <th class="text-center">Time In</th>
                                    <th class="text-center">Time Out</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody id="scan-tbody">
                                <tr id="empty-row">
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Waiting for students to scan...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <a href="{{ route('teacher.attendance.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to Attendance
                </a>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const pollUrl   = "{{ route('teacher.qrcode.scans', $qrCode) }}";
    const tbody     = document.getElementById('scan-tbody');
    const emptyRow  = document.getElementById('empty-row');
    const countBadge= document.getElementById('scan-count');

    function renderRows(students) {
        if (students.length === 0) {
            tbody.innerHTML = `<tr id="empty-row"><td colspan="5" class="text-center text-muted py-4">Waiting for students to scan...</td></tr>`;
            countBadge.textContent = '0';
            return;
        }

        countBadge.textContent = students.length;

        tbody.innerHTML = students.map(s => `
            <tr>
                <td><strong>${s.name}</strong></td>
                <td>${s.student_id}</td>
                <td class="text-center">
                    ${s.time_in
                        ? `<span class="badge bg-success">${s.time_in}</span>`
                        : '<span class="text-muted">—</span>'}
                </td>
                <td class="text-center">
                    ${s.time_out
                        ? `<span class="badge bg-warning text-dark">${s.time_out}</span>`
                        : '<span class="badge bg-secondary">Working</span>'}
                </td>
                <td class="text-center">
                    <span class="badge bg-${s.status === 'present' ? 'success' : (s.status === 'late' ? 'warning text-dark' : 'secondary')}">
                        ${s.status.charAt(0).toUpperCase() + s.status.slice(1)}
                    </span>
                </td>
            </tr>
        `).join('');
    }

    // Poll every 4 seconds
    async function poll() {
        try {
            const res  = await fetch(pollUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            renderRows(data.scanned);
        } catch (e) {
            console.warn('Poll error', e);
        }
    }

    poll();
    setInterval(poll, 4000);
</script>
@endpush