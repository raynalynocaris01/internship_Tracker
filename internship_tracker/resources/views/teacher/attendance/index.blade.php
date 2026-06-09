@extends('layouts.app')

@section('title', 'Attendance Monitoring')

@section('content')
<div class="container">

    {{-- Stats Cards --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body text-center">
                    <h3>{{ $todayAttendance ?? 0 }}</h3>
                    <small>Total Today</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body text-center">
                    <h3>{{ $studentsClockedIn ?? 0 }}</h3>
                    <small>Currently Clocked In</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info">
                <div class="card-body text-center">
                    <h3>{{ $attendances->total() ?? 0 }}</h3>
                    <small>Total Records</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Today's Attendance by Section --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center"
             style="background-color: #216699; color: white;">
            <h4 class="mb-0">
                <i class="fas fa-calendar-day"></i>
                Today's Attendance &mdash; {{ \Carbon\Carbon::today()->format('F d, Y') }}
            </h4>
            <span class="badge bg-light text-dark fs-6">
                <i class="fas fa-sun text-warning"></i> AM &nbsp;|&nbsp;
                <i class="fas fa-moon text-primary"></i> PM &nbsp;|&nbsp;
                <i class="fas fa-clock text-danger"></i> OT
            </span>
        </div>

        <div class="card-body">
            @if(isset($sections) && $sections->count() > 0)

                <ul class="nav nav-tabs mb-3" id="sectionTabs" role="tablist">
                    @foreach($sections as $index => $section)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $index == 0 ? 'active' : '' }}"
                                    data-bs-toggle="tab"
                                    data-bs-target="#att-section-{{ Str::slug($section->name) }}"
                                    type="button" role="tab">
                                <i class="fas fa-users"></i> {{ $section->name }}
                                <span class="badge bg-secondary ms-1">{{ $section->students_count }}</span>
                            </button>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content">
                    @foreach($sections as $index => $section)
                        <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}"
                             id="att-section-{{ Str::slug($section->name) }}"
                             role="tabpanel">

                            @if($section->students->count() > 0)

                                {{-- QR Code Generator for this section --}}
                                @php
                                    $firstEntry     = $section->students->first();
                                    $sectionSubject = $firstEntry?->internship?->subject;
                                    $sectionId      = $firstEntry?->internship?->section_id;
                                    $subjectId      = $firstEntry?->internship?->subject_id;
                                @endphp
                                @if($sectionSubject && $sectionId)
                                <div class="d-flex align-items-center gap-2 mb-3 p-2 bg-light rounded">
                                    <span class="fw-bold text-muted small">
                                        <i class="fas fa-qrcode"></i> Generate QR:
                                    </span>
                                    <form method="POST" action="{{ route('teacher.qrcode.generate') }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="subject_id" value="{{ $subjectId }}">
                                        <input type="hidden" name="section_id" value="{{ $sectionId }}">
                                        <input type="hidden" name="session" value="AM">
                                        <button type="submit" class="btn btn-sm btn-outline-warning fw-bold">
                                            <i class="fas fa-sun"></i> AM QR
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('teacher.qrcode.generate') }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="subject_id" value="{{ $subjectId }}">
                                        <input type="hidden" name="section_id" value="{{ $sectionId }}">
                                        <input type="hidden" name="session" value="PM">
                                        <button type="submit" class="btn btn-sm btn-outline-primary fw-bold">
                                            <i class="fas fa-moon"></i> PM QR
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('teacher.qrcode.generate') }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="subject_id" value="{{ $subjectId }}">
                                        <input type="hidden" name="section_id" value="{{ $sectionId }}">
                                        <input type="hidden" name="session" value="OT">
                                        <button type="submit" class="btn btn-sm btn-outline-danger fw-bold">
                                            <i class="fas fa-clock"></i> OT QR
                                        </button>
                                    </form>
                                </div>
                                @endif

                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Student</th>
                                                <th>Subject</th>
                                                {{-- AM columns --}}
                                                <th class="text-center" style="background:#fff8e1;">
                                                    <i class="fas fa-sun text-warning"></i> AM In
                                                </th>
                                                <th class="text-center" style="background:#fff8e1;">
                                                    <i class="fas fa-sun text-warning"></i> AM Out
                                                </th>
                                                <th class="text-center" style="background:#fff8e1;">AM Hrs</th>
                                                {{-- PM columns --}}
                                                <th class="text-center" style="background:#e8f4fd;">
                                                    <i class="fas fa-moon text-primary"></i> PM In
                                                </th>
                                                <th class="text-center" style="background:#e8f4fd;">
                                                    <i class="fas fa-moon text-primary"></i> PM Out
                                                </th>
                                                <th class="text-center" style="background:#e8f4fd;">PM Hrs</th>
                                                {{-- OT columns --}}
                                                <th class="text-center" style="background:#fce4ec;">
                                                    <i class="fas fa-clock text-danger"></i> OT In
                                                </th>
                                                <th class="text-center" style="background:#fce4ec;">
                                                    <i class="fas fa-clock text-danger"></i> OT Out
                                                </th>
                                                <th class="text-center" style="background:#fce4ec;">OT Hrs</th>
                                                <th class="text-center">Total Hrs</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($section->students as $entry)
                                            @php
                                                $student    = $entry->student;
                                                $internship = $entry->internship;
                                                $am         = $entry->amRecord;
                                                $pm         = $entry->pmRecord;
                                                $ot         = $entry->otRecord ?? null;

                                                $amHrs = $am?->hours_worked ?? 0;
                                                $pmHrs = $pm?->hours_worked ?? 0;
                                                $otHrs = $ot?->hours_worked ?? 0;
                                                $totalHrs = $amHrs + $pmHrs + $otHrs;

                                                $canAmOut  = $am && $am->time_in && !$am->time_out;
                                                $canPmOut  = $pm && $pm->time_in && !$pm->time_out;
                                                $canOtIn   = !$ot || !$ot->time_in;
                                                $canOtOut  = $ot && $ot->time_in && !$ot->time_out;

                                                // Time‑in buttons respect cut‑off times (11:50 AM for AM, 4:50 PM for PM)
                                                $now = \Carbon\Carbon::now();
                                                $amCutoff = \Carbon\Carbon::today()->setTime(11, 50);
                                                $pmCutoff = \Carbon\Carbon::today()->setTime(16, 50);
                                                $canAmIn = (!$am || !$am->time_in) && $now->lt($amCutoff);
                                                $canPmIn = (!$pm || !$pm->time_in) && $now->lt($pmCutoff);
                                            @endphp
                                            <tr>
                                                <td>
                                                    <strong>{{ $student->name }}</strong><br>
                                                    <small class="text-muted">{{ $student->student_id }}</small>
                                                </td>
                                                <td>
                                                    <strong>{{ $internship->subject->code ?? 'N/A' }}</strong><br>
                                                    <small class="text-muted">{{ $internship->subject->name ?? '' }}</small>
                                                </td>

                                                {{-- AM In --}}
                                                <td class="text-center" style="background:#fffde7;">
                                                    @if($am?->time_in)
                                                        <span class="badge bg-success">
                                                            {{ \Carbon\Carbon::parse($am->time_in)->format('h:i A') }}
                                                        </span>
                                                    @elseif($canAmIn)
                                                        <form method="POST" action="{{ route('teacher.students.attendance.timein', $student) }}">
                                                            @csrf
                                                            <input type="hidden" name="session" value="AM">
                                                            <button type="submit" class="btn btn-sm btn-outline-warning fw-bold">
                                                                <i class="fas fa-sign-in-alt"></i> AM In
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>

                                                {{-- AM Out --}}
                                                <td class="text-center" style="background:#fffde7;">
                                                    @if($am?->time_out)
                                                        <span class="badge bg-warning text-dark">
                                                            {{ \Carbon\Carbon::parse($am->time_out)->format('h:i A') }}
                                                        </span>
                                                    @elseif($canAmOut)
                                                        <form method="POST" action="{{ route('teacher.students.attendance.timeout', $student) }}">
                                                            @csrf
                                                            <input type="hidden" name="session" value="AM">
                                                            <button type="submit" class="btn btn-sm btn-warning fw-bold">
                                                                <i class="fas fa-sign-out-alt"></i> AM Out
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>

                                                {{-- AM Hours --}}
                                                <td class="text-center" style="background:#fffde7;">
                                                    @if($amHrs > 0)
                                                        <strong>{{ number_format($amHrs, 1) }}</strong>
                                                    @else
                                                        <span class="text-muted">0.0</span>
                                                    @endif
                                                </td>

                                                {{-- PM In --}}
                                                <td class="text-center" style="background:#e3f2fd;">
                                                    @if($pm?->time_in)
                                                        <span class="badge bg-primary">
                                                            {{ \Carbon\Carbon::parse($pm->time_in)->format('h:i A') }}
                                                        </span>
                                                    @elseif($canPmIn)
                                                        <form method="POST" action="{{ route('teacher.students.attendance.timein', $student) }}">
                                                            @csrf
                                                            <input type="hidden" name="session" value="PM">
                                                            <button type="submit" class="btn btn-sm btn-outline-primary fw-bold">
                                                                <i class="fas fa-sign-in-alt"></i> PM In
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>

                                                {{-- PM Out --}}
                                                <td class="text-center" style="background:#e3f2fd;">
                                                    @if($pm?->time_out)
                                                        <span class="badge bg-info text-dark">
                                                            {{ \Carbon\Carbon::parse($pm->time_out)->format('h:i A') }}
                                                        </span>
                                                    @elseif($canPmOut)
                                                        <form method="POST" action="{{ route('teacher.students.attendance.timeout', $student) }}">
                                                            @csrf
                                                            <input type="hidden" name="session" value="PM">
                                                            <button type="submit" class="btn btn-sm btn-info fw-bold">
                                                                <i class="fas fa-sign-out-alt"></i> PM Out
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>

                                                {{-- PM Hours --}}
                                                <td class="text-center" style="background:#e3f2fd;">
                                                    @if($pmHrs > 0)
                                                        <strong>{{ number_format($pmHrs, 1) }}</strong>
                                                    @else
                                                        <span class="text-muted">0.0</span>
                                                    @endif
                                                </td>

                                                {{-- OT In --}}
                                                <td class="text-center" style="background:#fce4ec;">
                                                    @if($ot?->time_in)
                                                        <span class="badge bg-danger">
                                                            {{ \Carbon\Carbon::parse($ot->time_in)->format('h:i A') }}
                                                        </span>
                                                    @elseif($canOtIn)
                                                        <form method="POST" action="{{ route('teacher.students.attendance.timein', $student) }}">
                                                            @csrf
                                                            <input type="hidden" name="session" value="OT">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger fw-bold">
                                                                <i class="fas fa-sign-in-alt"></i> OT In
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>

                                                {{-- OT Out --}}
                                                <td class="text-center" style="background:#fce4ec;">
                                                    @if($ot?->time_out)
                                                        <span class="badge bg-dark text-white">
                                                            {{ \Carbon\Carbon::parse($ot->time_out)->format('h:i A') }}
                                                        </span>
                                                    @elseif($canOtOut)
                                                        <form method="POST" action="{{ route('teacher.students.attendance.timeout', $student) }}">
                                                            @csrf
                                                            <input type="hidden" name="session" value="OT">
                                                            <button type="submit" class="btn btn-sm btn-dark fw-bold">
                                                                <i class="fas fa-sign-out-alt"></i> OT Out
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>

                                                {{-- OT Hours --}}
                                                <td class="text-center" style="background:#fce4ec;">
                                                    @if($otHrs > 0)
                                                        <strong>{{ number_format($otHrs, 1) }}</strong>
                                                    @else
                                                        <span class="text-muted">0.0</span>
                                                    @endif
                                                </td>

                                                {{-- Total Hours --}}
                                                <td class="text-center">
                                                    <strong class="{{ $totalHrs > 0 ? 'text-success' : 'text-muted' }}">
                                                        {{ number_format($totalHrs, 1) }}
                                                    </strong>
                                                </td>

                                                {{-- Actions --}}
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('teacher.students.attendance.create', $student) }}"
                                                           class="btn btn-secondary" title="Manual Entry">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                        <a href="{{ route('teacher.attendance.by_student', $student->id) }}"
                                                           class="btn btn-info" title="View History">
                                                            <i class="fas fa-history"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-users fa-2x mb-2 d-block"></i>
                                    No students in this section yet.
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

            @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-users fa-3x mb-3 d-block"></i>
                    No sections assigned to you yet.
                </div>
            @endif
        </div>
    </div>

    {{-- Full Attendance Log --}}
    <div class="card">
        <div class="card-header" style="background-color: #6c757d; color: white;">
            <h5 class="mb-0"><i class="fas fa-list"></i> Full Attendance Log</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Student</th>
                            <th>Section</th>
                            <th>Subject</th>
                            <th class="text-center">Session</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Hours</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($attendance->date)->format('M d, Y') }}</td>
                            <td>{{ $attendance->student->name }}<br><small class="text-muted">{{ $attendance->student->student_id }}</small></td>
                            <td>{{ $attendance->internship->section->name ?? '—' }}</td>
                            <td>{{ $attendance->internship->subject->code ?? 'N/A' }}</td>
                            <td class="text-center">
                                @if($attendance->session == 'AM')
                                    <span class="badge bg-warning text-dark"><i class="fas fa-sun"></i> AM</span>
                                @elseif($attendance->session == 'PM')
                                    <span class="badge bg-primary"><i class="fas fa-moon"></i> PM</span>
                                @else
                                    <span class="badge bg-danger"><i class="fas fa-clock"></i> OT</span>
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
                            <td>{{ number_format($attendance->hours_worked, 2) }} hrs</p>
                            <td>
                                <span class="badge bg-{{ $attendance->status == 'present' ? 'success' : ($attendance->status == 'late' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($attendance->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('teacher.attendance.show', $attendance) }}" class="btn btn-sm btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="fas fa-clock fa-3x mb-3 d-block"></i>
                                    No attendance records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $attendances->links() }}</div>
        </div>
    </div>

</div>
@endsection