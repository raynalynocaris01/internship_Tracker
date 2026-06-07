@extends('layouts.app')

@section('title', 'Attendance Management')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #216699; color: white;">
            <h4 class="mb-0">
                <i class="fas fa-clock"></i> Attendance Management
            </h4>
            <div>
                <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <button type="button" class="btn btn-success btn-sm" onclick="window.print()">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Student</th>
                            <th>Student ID</th>
                            <th>Subject</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Hours</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($attendance->date)->format('M d, Y') }}<br>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($attendance->date)->format('l') }}</small>
                            </td>
                            <td>
                                {{ $attendance->student->name ?? 'N/A' }}<br>
                                <small class="text-muted">{{ $attendance->student->email ?? '' }}</small>
                            </td>
                            <td>{{ $attendance->student->student_id ?? 'N/A' }}</p>
                            <td>{{ $attendance->internship->subject->code ?? 'N/A' }}<br>
                                <small>{{ $attendance->internship->subject->name ?? '' }}</small>
                            </p>
                            <td>{{ \Carbon\Carbon::parse($attendance->time_in)->format('h:i A') }}</p>
                            <td>
                                @if($attendance->time_out)
                                    {{ \Carbon\Carbon::parse($attendance->time_out)->format('h:i A') }}
                                @else
                                    <span class="badge bg-warning">Not yet</span>
                                @endif
                            </p>
                            <td><strong>{{ number_format($attendance->hours_worked, 2) }}</strong> hrs</p>
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
                            </p>
                            <td>
                                <a href="{{ route('admin.attendances.show', $attendance) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </p>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                                    <p>No attendance records found.</p>
                                </p>
                            </tr>
                        @endforelse
                    </tbody>
                </div>
            </div>

            <div class="mt-3">
                {{ $attendances->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="GET" action="{{ route('admin.attendances.index') }}">
                <div class="modal-header" style="background-color: #216699; color: white;">
                    <h5 class="modal-title"><i class="fas fa-filter"></i> Filter Attendance</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Date Range</label>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="date" name="start_date" class="form-control" placeholder="Start Date">
                            </div>
                            <div class="col-md-6">
                                <input type="date" name="end_date" class="form-control" placeholder="End Date">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Student</label>
                        <select name="student_id" class="form-control">
                            <option value="">All Students</option>
                            @foreach($students ?? [] as $student)
                                <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->student_id }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <select name="subject_id" class="form-control">
                            <option value="">All Subjects</option>
                            @foreach($subjects ?? [] as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->code }} - {{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="present">Present</option>
                            <option value="late">Late</option>
                            <option value="half_day">Half Day</option>
                            <option value="absent">Absent</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection