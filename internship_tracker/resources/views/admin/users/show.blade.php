@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header" style="background-color: #216699; color: white;">
            <h4 class="mb-0">
                <i class="fas fa-user-circle"></i> User Details: {{ $user->name }}
            </h4>
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
                            </p>
                        </tr>
                        @if($user->role == 'student')
                        <tr>
                            <th>Student ID</th>
                            <td>{{ $user->student_id ?? 'N/A' }}</p>
                        </tr>
                        <tr>
                            <th>Course</th>
                            <td>{{ $user->course ?? 'N/A' }}</p>
                        </tr>
                        <tr>
                            <th>Year Level</th>
                            <td>{{ $user->year_level ?? 'N/A' }} Year</p>
                        </tr>
                        @elseif($user->role == 'teacher')
                        <tr>
                            <th>Teacher ID</th>
                            <td>{{ $user->teacher_id ?? 'N/A' }}</p>
                        </tr>
                        @endif
                        <tr>
                            <th>Department</th>
                            <td>{{ $user->department ?? 'N/A' }}</p>
                        </tr>
                        <tr>
                            <th>Member Since</th>
                            <td>{{ $user->created_at->format('F d, Y') }}</p>
                        </tr>
                    </div>
                </div>
                <div class="col-md-6">
                    @if($user->role == 'student')
                        <div class="card">
                            <div class="card-header" style="background-color: #28a745; color: white;">
                                <h5 class="mb-0">
                                    <i class="fas fa-chart-line"></i> Internship Statistics
                                </h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Total Hours Completed:</strong> 
                                    <strong class="text-primary">{{ number_format($totalHours ?? 0, 2) }}</strong> hours
                                </p>
                                <p><strong>Total Attendance Days:</strong> 
                                    <strong class="text-info">{{ $attendanceCount ?? 0 }}</strong> days
                                </p>
                                <p><strong>Active Internship:</strong> 
                                    @if(($activeInternship ?? null) || ($user->active_internship ?? false))
                                        <span class="badge bg-success">Yes</span>
                                        <br>
                                        <small class="text-muted">
                                            Subject: {{ ($activeInternship ?? $user->active_internship)?->subject->code ?? 'N/A' }}
                                        </small>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </p>
                                @if(($user->internships ?? collect())->count() > 0)
                                    <hr>
                                    <p><strong>All Internships:</strong></p>
                                    <ul class="list-group">
                                        @foreach($user->internships as $internship)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>{{ $internship->subject->code ?? 'N/A' }}</strong>
                                                    <br>
                                                    <small>{{ $internship->status }}</small>
                                                </div>
                                                <span class="badge bg-primary rounded-pill">{{ $internship->progress }}%</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
                @if($user->id !== auth()->id())
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this user? This will also delete all associated data.')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                @endif
                @if($user->role == 'student' && !($activeInternship ?? false) && !($user->active_internship ?? false))
                    <a href="{{ route('admin.internships.create') }}?student_id={{ $user->id }}" class="btn btn-primary">
                        <i class="fas fa-briefcase"></i> Assign Internship
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection