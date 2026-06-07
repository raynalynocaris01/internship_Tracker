@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3>User Details: {{ $user->name }}</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Name</th>
                            <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>Role</th>
                            <td>
                                @if($user->role == 'admin')
                                    <span class="badge bg-danger">Administrator</span>
                                @elseif($user->role == 'teacher')
                                    <span class="badge bg-warning">Teacher</span>
                                @else
                                    <span class="badge bg-success">Student</span>
                                @endif
                            </td>
                        </tr>
                        @if($user->role == 'student')
                        <tr>
                            <th>Student ID</th>
                            <td>{{ $user->student_id ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Course</th>
                            <td>{{ $user->course ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Year Level</th>
                            <td>{{ $user->year_level ?? 'N/A' }} Year</td>
                        </tr>
                        @elseif($user->role == 'teacher')
                        <tr>
                            <th>Teacher ID</th>
                            <td>{{ $user->teacher_id ?? 'N/A' }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Department</th>
                            <td>{{ $user->department ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Member Since</th>
                            <td>{{ $user->created_at->format('F d, Y') }}</td>
                        </tr>
                     </>
                </div>
                <div class="col-md-6">
                    @if($user->role == 'student')
                        <div class="card">
                            <div class="card-header">
                                <h5>Internship Statistics</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Total Hours Completed:</strong> {{ number_format($totalHours ?? 0, 2) }} hours</p>
                                <p><strong>Total Attendance Days:</strong> {{ $attendanceCount ?? 0 }} days</p>
                                <p><strong>Active Enrollment:</strong> 
                                    @if($user->activeInternship)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Back</a>
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">Edit</a>
                @if($user->id !== auth()->id())
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this user?')">Delete</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection