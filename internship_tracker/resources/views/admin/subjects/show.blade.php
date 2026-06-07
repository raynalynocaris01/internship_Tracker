@extends('layouts.app')

@section('title', 'Subject Details')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header" style="background-color: #216699; color: white;">
            <h4 class="mb-0">
                <i class="fas fa-book"></i> Subject Details: {{ $subject->code }}
            </h4>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Left Column - Subject Information -->
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="35%">Subject Code</th>
                            <td>{{ $subject->code }}</td>
                        </tr>
                        <tr>
                            <th>Subject Name</th>
                            <td>{{ $subject->name }}</td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>{{ $subject->description ?? 'No description' }}</td>
                        </tr>
                        <tr>
                            <th>Units</th>
                            <td>{{ $subject->units }}</td>
                        </tr>
                        <tr>
                            <th>Required Hours</th>
                            <td>{{ number_format($subject->required_hours) }} hours</td>
                        </tr>
                        <tr>
                            <th>Semester</th>
                            <td>{{ $subject->semester }} Semester</td>
                        </tr>
                        <tr>
                            <th>School Year</th>
                            <td>{{ $subject->school_year }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($subject->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $subject->created_at->format('F d, Y h:i A') }}</td>
                        </tr>
                    </table>
                </div>

                <!-- Right Column - Statistics and Assignments -->
                <div class="col-md-6">
                    <!-- Internship Statistics Card -->
                    <div class="card mb-3">
                        <div class="card-header" style="background-color: #28a745; color: white;">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-line"></i> Internship Statistics
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center mb-3">
                                <div class="col-4">
                                    <h3 class="text-primary">{{ $subject->internships->count() }}</h3>
                                    <small class="text-muted">Total Internships</small>
                                </div>
                                <div class="col-4">
                                    <h3 class="text-success">{{ $subject->internships->where('status', 'active')->count() }}</h3>
                                    <small class="text-muted">Active</small>
                                </div>
                                <div class="col-4">
                                    <h3 class="text-info">{{ $subject->internships->where('status', 'completed')->count() }}</h3>
                                    <small class="text-muted">Completed</small>
                                </div>
                            </div>
                            <hr>
                            <div class="mt-3">
                                <p><strong>Total Hours Rendered:</strong> 
                                    <strong>{{ number_format($subject->internships->sum('total_hours_rendered')) }} / {{ number_format($subject->required_hours * $subject->internships->count()) }} hours</strong>
                                </p>
                                <div class="progress mb-2" style="height: 10px;">
                                    @php
                                        $totalProgress = $subject->internships->count() > 0 
                                            ? ($subject->internships->sum('total_hours_rendered') / ($subject->required_hours * $subject->internships->count())) * 100 
                                            : 0;
                                    @endphp
                                    <div class="progress-bar bg-success" style="width: {{ $totalProgress }}%"></div>
                                </div>
                                <p class="text-muted small">Overall progress across all internships</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Teacher & Section Assignments Card -->
                    <div class="card">
                        <div class="card-header" style="background-color: #17a2b8; color: white;">
                            <h5 class="mb-0">
                                <i class="fas fa-chalkboard-user"></i> Teacher & Section Assignments
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($subject->sections && $subject->sections->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Section</th>
                                                <th>Teacher</th>
                                                <th>Status</th>
                                                <th>Assigned Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($subject->sections as $assignment)
                                            @php
                                                $teacher = \App\Models\User::find($assignment->pivot->teacher_id);
                                            @endphp
                                            <tr>
                                                <td>
                                                    <strong>{{ $assignment->name }}</strong><br>
                                                    <small class="text-muted">{{ $assignment->course }} - Year {{ $assignment->year_level }}</small>
                                                 </p>
                                                <td>
                                                    @if($teacher)
                                                        {{ $teacher->name }}<br>
                                                        <small class="text-muted">{{ $teacher->teacher_id }}</small>
                                                    @else
                                                        <span class="badge bg-warning">No Teacher Assigned</span>
                                                    @endif
                                                 </p>
                                                <td>
                                                    <span class="badge bg-{{ $assignment->pivot->status == 'active' ? 'success' : 'danger' }}">
                                                        {{ ucfirst($assignment->pivot->status) }}
                                                    </span>
                                                 </p>
                                                <td>
                                                    {{ $assignment->pivot->created_at ? $assignment->pivot->created_at->format('M d, Y') : 'N/A' }}
                                                 </p>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-chalkboard-user fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No teacher assignments yet.</p>
                                    <a href="{{ route('admin.subjects.edit', $subject) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus"></i> Add Teacher Assignment
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-between">
                <div>
                    <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
                <div>
                    <a href="{{ route('admin.subjects.edit', $subject) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Subject
                    </a>
                    <a href="{{ route('admin.internships.create') }}?subject_id={{ $subject->id }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Assign Internship
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection