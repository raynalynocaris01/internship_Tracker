@extends('layouts.app')

@section('title', 'Student Details')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4 mb-4">
            <!-- Student Information Card -->
            <div class="card">
                <div class="card-header" style="background-color: #216699; color: white;">
                    <h5 class="mb-0">
                        <i class="fas fa-user-graduate"></i> Student Information
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Student ID</th>
                            <td>{{ $student->student_id ?? 'N/A' }}</p>
                        </tr>
                        <tr>
                            <th>Full Name</th>
                            <td>{{ $student->name }}</p>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $student->email }}</p>
                        </tr>
                        <tr>
                            <th>Course</th>
                            <td>{{ $student->course ?? 'N/A' }}</p>
                        </tr>
                        <td>
                            <th>Year Level</th>
                            <td>{{ $student->year_level ?? 'N/A' }} Year</p>
                        </tr>
                        <tr>
                            <th>Department</th>
                            <td>{{ $student->department ?? 'N/A' }}</p>
                        </tr>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <!-- Internship Information Card -->
            <div class="card">
                <div class="card-header" style="background-color: #28a745; color: white;">
                    <h5 class="mb-0">
                        <i class="fas fa-briefcase"></i> Internship Information
                    </h5>
                </div>
                <div class="card-body">
                    @if($internship)
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Subject:</strong> {{ $internship->subject->code }} - {{ $internship->subject->name }}</p>
                                <p><strong>Required Hours:</strong> {{ number_format($internship->subject->required_hours) }} hours</p>
                                <p><strong>Total Hours Rendered:</strong> {{ number_format($totalHours, 1) }} hours</p>
                                @if($internship->company_name)
                                    <p><strong>Company:</strong> {{ $internship->company_name }}</p>
                                @endif
                                @if($internship->position)
                                    <p><strong>Position:</strong> {{ $internship->position }}</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <p><strong>Status:</strong> 
                                    <span class="badge bg-{{ $internship->status_badge }}">
                                        {{ $internship->status_label }}
                                    </span>
                                </p>
                                <p><strong>Progress:</strong></p>
                                <div class="progress mb-3" style="height: 30px;">
                                    <div class="progress-bar bg-success" style="width: {{ $progress }}%">
                                        {{ number_format($progress, 1) }}%
                                    </div>
                                </div>
                                <p><strong>Remaining Hours:</strong> {{ number_format($internship->subject->required_hours - $totalHours, 1) }} hours</p>
                                @if($internship->start_date)
                                    <p><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($internship->start_date)->format('F d, Y') }}</p>
                                @endif
                                @if($internship->completion_date)
                                    <p><strong>Completion Date:</strong> {{ \Carbon\Carbon::parse($internship->completion_date)->format('F d, Y') }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Quick Time In / Time Out Buttons -->
                        @if($internship->status == 'active')
                        <div class="mt-3 d-flex gap-2">
                            <form method="POST" action="{{ route('teacher.students.attendance.timein', $student) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-sign-in-alt"></i> Time In
                                </button>
                            </form>

                            <form method="POST" action="{{ route('teacher.students.attendance.timeout', $student) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-sign-out-alt"></i> Time Out
                                </button>
                            </form>

                            <a href="{{ route('teacher.students.attendance.create', $student) }}" class="btn btn-primary">
                                <i class="fas fa-clock"></i> Manual Entry
                            </a>
                        </div>
                        @endif

                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> 
                            Student is not assigned to any internship. Please assign them first.
                            <div class="mt-2">
                                <a href="{{ route('admin.internships.create') }}?student_id={{ $student->id }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus"></i> Assign Internship
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance History -->
    <div class="card">
        <div class="card-header" style="background-color: #17a2b8; color: white;">
            <h5 class="mb-0">
                <i class="fas fa-clock"></i> Attendance History
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Hours Worked</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($attendance->date)->format('F d, Y') }}</p>
                            <td>{{ \Carbon\Carbon::parse($attendance->time_in)->format('h:i A') }}</p>
                            <td>{{ $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('h:i A') : 'Still working' }}</p>
                            <td>{{ number_format($attendance->hours_worked, 2) }} hrs</p>
                            <td>
                                <span class="badge bg-{{ $attendance->status == 'present' ? 'success' : ($attendance->status == 'late' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($attendance->status) }}
                                </span>
                            </p>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No attendance records yet.</p>
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

    <div class="mt-3">
        <a href="{{ route('teacher.students.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Students
        </a>
        @if($internship && $internship->status == 'active')
            <a href="{{ route('admin.internships.edit', $internship) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Internship
            </a>
        @endif
        <!-- The Manual button is now inside the internship card, so we remove the duplicate below -->
    </div>
</div>
@endsection