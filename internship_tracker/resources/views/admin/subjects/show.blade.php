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
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Subject Code</th>
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
                            <td>{{ number_format($subject->required_hours) }} hours</p></td>
                        </tr>
                        <tr>
                            <th>Semester</th>
                            <td>{{ $subject->semester }} Semester</p></td>
                        </tr>
                        <tr>
                            <th>School Year</th>
                            <td>{{ $subject->school_year }}</p></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($subject->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </p>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $subject->created_at->format('F d, Y h:i A') }}</p></td>
                        </tr>
                    vat
                </div>
                <div class="col-md-6">
                    <div class="card">
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
                    
                    <!-- Assigned Sections -->
                    <div class="card mt-3">
                        <div class="card-header" style="background-color: #17a2b8; color: white;">
                            <h5 class="mb-0">
                                <i class="fas fa-users"></i> Assigned Sections
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($subject->sections && $subject->sections->count() > 0)
                                <ul class="list-group">
                                    @foreach($subject->sections as $section)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $section->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $section->course }} - Year {{ $section->year_level }}</small>
                                            </div>
                                            <span class="badge bg-primary rounded-pill">{{ $section->pivot->status ?? 'active' }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted text-center mb-0">No sections assigned to this subject yet.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <a href="{{ route('admin.subjects.edit', $subject) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('admin.internships.create') }}?subject_id={{ $subject->id }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Assign Internship
                </a>
            </div>
        </div>
    </div>
</div>
@endsection