@extends('layouts.app')

@section('title', 'Student Details')

@section('content')
<div class="container py-4">
    <div class="row g-4">
        <!-- Student Information Card -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-primary text-white rounded-top-4 d-flex align-items-center gap-2">
                    <i class="fas fa-user-graduate fa-lg"></i>
                    <h5 class="mb-0">Student Information</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="fw-semibold">Student ID</span>
                            <span>{{ $student->student_id ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="fw-semibold">Full Name</span>
                            <span>{{ $student->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="fw-semibold">Email</span>
                            <span>{{ $student->email }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="fw-semibold">Course</span>
                            <span>{{ $student->course ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="fw-semibold">Year Level</span>
                            <span>{{ $student->year_level ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="fw-semibold">Department</span>
                            <span>{{ $student->department ?? 'N/A' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Internship Information & Quick Actions -->
        <div class="col-lg-8">
            @if($internship)
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header bg-success text-white rounded-top-4 d-flex align-items-center gap-2">
                        <i class="fas fa-briefcase fa-lg"></i>
                        <h5 class="mb-0">Internship Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <dl class="row mb-0">
                                    <dt class="col-sm-5">Subject</dt>
                                    <dd class="col-sm-7">{{ $internship->subject->code }} - {{ $internship->subject->name }}</dd>

                                    <dt class="col-sm-5">Required Hours</dt>
                                    <dd class="col-sm-7">{{ number_format($internship->subject->required_hours) }} hrs</dd>

                                    <dt class="col-sm-5">Total Rendered</dt>
                                    <dd class="col-sm-7">{{ number_format($totalHours, 1) }} hrs</dd>

                                    @if($internship->company_name)
                                        <dt class="col-sm-5">Company</dt>
                                        <dd class="col-sm-7">{{ $internship->company_name }}</dd>
                                    @endif
                                    @if($internship->position)
                                        <dt class="col-sm-5">Position</dt>
                                        <dd class="col-sm-7">{{ $internship->position }}</dd>
                                    @endif
                                </dl>
                            </div>
                            <div class="col-md-6">
                                <dl class="row mb-0">
                                    <dt class="col-sm-5">Status</dt>
                                    <dd class="col-sm-7">
                                        <span class="badge bg-{{ $internship->status_badge }} px-3 py-2">
                                            {{ $internship->status_label }}
                                        </span>
                                    </dd>

                                    <dt class="col-sm-5">Progress</dt>
                                    <dd class="col-sm-7">
                                        <div class="progress mb-2" style="height: 10px;">
                                            <div class="progress-bar bg-success" style="width: {{ $progress }}%"></div>
                                        </div>
                                        <span class="small">{{ number_format($progress, 1) }}%</span>
                                    </dd>

                                    <dt class="col-sm-5">Remaining Hours</dt>
                                    <dd class="col-sm-7">{{ number_format($internship->subject->required_hours - $totalHours, 1) }} hrs</dd>

                                    @if($internship->start_date)
                                        <dt class="col-sm-5">Start Date</dt>
                                        <dd class="col-sm-7">{{ \Carbon\Carbon::parse($internship->start_date)->format('F d, Y') }}</dd>
                                    @endif
                                    @if($internship->completion_date)
                                        <dt class="col-sm-5">Completion Date</dt>
                                        <dd class="col-sm-7">{{ \Carbon\Carbon::parse($internship->completion_date)->format('F d, Y') }}</dd>
                                    @endif
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions for Active Internship -->
                @if($internship->status == 'active')
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-warning text-dark rounded-top-4 d-flex align-items-center gap-2">
                        <i class="fas fa-clock fa-lg"></i>
                        <h5 class="mb-0">Quick Time Tracking</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center">
                            <div class="btn-group">
                                <form method="POST" action="{{ route('teacher.students.attendance.timein', $student) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="session" value="AM">
                                    <button type="submit" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-sun"></i> AM In
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('teacher.students.attendance.timeout', $student) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="session" value="AM">
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-sun"></i> AM Out
                                    </button>
                                </form>
                            </div>
                            <div class="btn-group">
                                <form method="POST" action="{{ route('teacher.students.attendance.timein', $student) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="session" value="PM">
                                    <button type="submit" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-moon"></i> PM In
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('teacher.students.attendance.timeout', $student) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="session" value="PM">
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-moon"></i> PM Out
                                    </button>
                                </form>
                            </div>
                            <a href="{{ route('teacher.students.attendance.create', $student) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-pen-alt"></i> Manual Entry
                            </a>
                        </div>
                        <hr class="my-3">
                        <div class="small text-muted text-center">
                            <i class="fas fa-info-circle"></i> Use AM/PM buttons for quick attendance. Manual entry for corrections.
                        </div>
                    </div>
                </div>
                @endif
            @else
                <div class="alert alert-warning shadow-sm rounded-4">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Student is not assigned to any internship. Please assign them first.
                    <div class="mt-2">
                        <a href="{{ route('admin.internships.create') }}?student_id={{ $student->id }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Assign Internship
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Attendance History -->
    <div class="card shadow-sm border-0 rounded-4 mt-4">
        <div class="card-header bg-info text-white rounded-top-4 d-flex align-items-center gap-2">
            <i class="fas fa-history fa-lg"></i>
            <h5 class="mb-0">Attendance History</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Hours</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($attendance->date)->format('M d, Y') }}<br>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($attendance->date)->format('D') }}</small>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($attendance->time_in)->format('h:i A') }}<br>
                                <small class="text-muted">{{ $attendance->session ?? 'AM/PM' }}</small>
                            </td>
                            <td>
                                @if($attendance->time_out)
                                    {{ \Carbon\Carbon::parse($attendance->time_out)->format('h:i A') }}
                                @else
                                    <span class="badge bg-warning text-dark">In progress</span>
                                @endif
                            </td>
                            <td>{{ number_format($attendance->hours_worked, 2) }} hrs</p>
                            <td>
                                <span class="badge bg-{{ $attendance->status == 'present' ? 'success' : ($attendance->status == 'late' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($attendance->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-clock fa-2x mb-2 d-block"></i>
                                    No attendance records yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0">
                {{ $attendances->links() }}
            </div>
        </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <a href="{{ route('teacher.students.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Students
        </a>
        @if($internship && $internship->status == 'active')
            <a href="{{ route('admin.internships.edit', $internship) }}" class="btn btn-outline-warning">
                <i class="fas fa-edit"></i> Edit Internship
            </a>
        @endif
    </div>
</div>
@endsection