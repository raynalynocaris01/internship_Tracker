@extends('layouts.app')

@section('title', 'Internship Details')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header" style="background-color: #216699; color: white;">
            <h4 class="mb-0">
                <i class="fas fa-briefcase"></i> Internship Details
            </h4>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="35%">Internship ID</th>
                            <td>{{ $internship->id }}</td>
                        </tr>
                        <tr>
                            <th>Student Name</th>
                            <td>{{ $internship->student->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Student ID</th>
                            <td>{{ $internship->student->student_id ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Subject</th>
                            <td>{{ $internship->subject->code ?? 'N/A' }} - {{ $internship->subject->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Required Hours</th>
                            <td>{{ number_format($internship->subject->required_hours ?? 0) }} hours</td>
                        </tr>
                        <tr>
                            <th>Teacher/Supervisor</th>
                            <td>{{ $internship->teacher->name ?? 'Not Assigned' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="35%">Section</th>
                            <td>{{ $internship->section->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Company</th>
                            <td>{{ $internship->company_name ?? 'School-based' }}</td>
                        </tr>
                        <tr>
                            <th>Position</th>
                            <td>{{ $internship->position ?? 'Intern' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($internship->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($internship->status == 'completed')
                                    <span class="badge bg-info">Completed</span>
                                @elseif($internship->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @else
                                    <span class="badge bg-danger">Dropped</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Start Date</th>
                            <td>{{ $internship->start_date ? date('F d, Y', strtotime($internship->start_date)) : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Completion Date</th>
                            <td>{{ $internship->completion_date ? date('F d, Y', strtotime($internship->completion_date)) : 'Not yet completed' }}</td>
                        </tr>
                        <tr>
                            <th>Total Hours Rendered</th>
                            <td><strong>{{ number_format($totalHours, 2) }} / {{ number_format($internship->subject->required_hours ?? 0) }} hours</strong></td>
                        </tr>
                        <tr>
                            <th>Progress</th>
                            <td>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar bg-success" style="width: {{ $progress }}%">
                                        {{ number_format($progress, 1) }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                    更好
                </div>
            </div>

            @if($internship->remarks)
                <div class="alert alert-info mt-3">
                    <strong>Remarks:</strong> {{ $internship->remarks }}
                </div>
            @endif

            <div class="mt-4">
                <a href="{{ route('admin.internships.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Internships
                </a>
                <a href="{{ route('admin.internships.edit', $internship) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit Internship
                </a>
            </div>
        </div>
    </div>

    <!-- Attendance Records for this Internship -->
    <div class="card mt-4">
        <div class="card-header" style="background-color: #17a2b8; color: white;">
            <h5 class="mb-0">
                <i class="fas fa-clock"></i> Attendance Records
            </h5>
        </div>
        <div class="card-body">
            @if($internship->attendances && $internship->attendances->count() > 0)
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
                            @foreach($internship->attendances as $attendance)
                            <tr>
                                <td>{{ $attendance->date ? date('F d, Y', strtotime($attendance->date)) : 'N/A' }}</td>
                                <td>{{ $attendance->time_in ? date('h:i A', strtotime($attendance->time_in)) : 'N/A' }}</td>
                                <td>{{ $attendance->time_out ? date('h:i A', strtotime($attendance->time_out)) : 'Not yet' }}</td>
                                <td>{{ number_format($attendance->hours_worked, 2) }} hrs</td>
                                <td>
                                    <span class="badge bg-{{ $attendance->status == 'present' ? 'success' : ($attendance->status == 'late' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($attendance->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <small class="text-muted">Total attendance records: {{ $internship->attendances->count() }}</small>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No attendance records yet for this internship.</p>
                    <p class="text-muted small">Students need to scan their QR code to record attendance.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Summary Card -->
    <div class="card mt-4">
        <div class="card-header" style="background-color: #28a745; color: white;">
            <h5 class="mb-0">
                <i class="fas fa-chart-line"></i> Internship Summary
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center">
                    <h3>{{ number_format($totalHours, 1) }}</h3>
                    <p class="text-muted">Hours Completed</p>
                </div>
                <div class="col-md-3 text-center">
                    <h3>{{ $internship->attendances->count() }}</h3>
                    <p class="text-muted">Days Present</p>
                </div>
                <div class="col-md-3 text-center">
                    <h3>{{ number_format($progress, 1) }}%</h3>
                    <p class="text-muted">Overall Progress</p>
                </div>
                <div class="col-md-3 text-center">
                    <h3>{{ number_format($internship->subject->required_hours - $totalHours, 1) }}</h3>
                    <p class="text-muted">Remaining Hours</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection