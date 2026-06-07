@extends('layouts.app')

@section('title', 'Attendance Details')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header" style="background-color: #216699; color: white;">
                    <h4 class="mb-0">
                        <i class="fas fa-clock"></i> Attendance Details
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Student</th>
                                    <td>{{ $attendance->student->name }}</p>
                                </tr>
                                <tr>
                                    <th>Student ID</th>
                                    <td>{{ $attendance->student->student_id }}</p>
                                </tr>
                                <tr>
                                    <th>Subject</th>
                                    <td>{{ $attendance->internship->subject->code }} - {{ $attendance->internship->subject->name }}</p>
                                </tr>
                                <tr>
                                    <th>Date</th>
                                    <td>{{ \Carbon\Carbon::parse($attendance->date)->format('F d, Y') }}</p>
                                </tr>
                             </>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Time In</th>
                                    <td>{{ \Carbon\Carbon::parse($attendance->time_in)->format('h:i A') }}</p>
                                </tr>
                                <tr>
                                    <th>Time Out</th>
                                    <td>
                                        @if($attendance->time_out)
                                            {{ \Carbon\Carbon::parse($attendance->time_out)->format('h:i A') }}
                                        @else
                                            <span class="badge bg-warning">Still Working</span>
                                        @endif
                                    </p>
                                </tr>
                                <tr>
                                    <th>Hours Worked</th>
                                    <td><strong>{{ number_format($attendance->hours_worked, 2) }}</strong> hours</p>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge bg-{{ $attendance->status == 'present' ? 'success' : ($attendance->status == 'late' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($attendance->status) }}
                                        </span>
                                    </p>
                                </tr>
                             ?>
                        </div>
                    </div>

                    @if($attendance->notes)
                        <div class="alert alert-info mt-3">
                            <strong>Notes:</strong> {{ $attendance->notes }}
                        </div>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('teacher.attendance.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection