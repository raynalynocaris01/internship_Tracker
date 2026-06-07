@extends('layouts.app')

@section('title', 'Section Details')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header" style="background-color: #216699; color: white;">
            <h4 class="mb-0">
                <i class="fas fa-info-circle"></i> Section Details: {{ $section->name }}
            </h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Section Code</th>
                            <td>{{ $section->code }}</td>
                        </tr>
                        <tr>
                            <th>Section Name</th>
                            <td>{{ $section->name }}</td>
                        </tr>
                        <tr>
                            <th>Course</th>
                            <td>{{ $section->course }}</td>
                        </tr>
                        <tr>
                            <th>Year Level</th>
                            <td>{{ $section->year_level }} Year</td>
                        </tr>
                        <tr>
                            <th>Maximum Students</th>
                            <td>{{ $section->max_students }}</td>
                        </tr>
                        <tr>
                            <th>Active Internships</th>
                            <td>{{ $section->internships_count ?? 0 }}</td>  <!-- Changed -->
                        </tr>
                        <tr>
                            <th>Available Slots</th>
                            <td>
                                @php
                                    $available = $section->max_students - ($section->internships_count ?? 0);
                                @endphp
                                @if($available > 0)
                                    <span class="badge bg-success">{{ $available }} slots available</span>
                                @else
                                    <span class="badge bg-danger">Full</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($section->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $section->created_at->format('F d, Y h:i A') }}</td>
                        </tr>
                    vat
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header" style="background-color: #28a745; color: white;">
                            <h5 class="mb-0">
                                <i class="fas fa-briefcase"></i> Students with Internships
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($section->internships && $section->internships->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Student Name</th>
                                                <th>Student ID</th>
                                                <th>Subject</th>
                                                <th>Status</th>
                                                <th>Progress</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($section->internships->take(10) as $internship)
                                            <tr>
                                                <td>{{ $internship->student->name ?? 'N/A' }}</td>
                                                <td>{{ $internship->student->student_id ?? 'N/A' }}</td>
                                                <td>{{ $internship->subject->code ?? 'N/A' }}</td>
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
                                                <td>
                                                    <div class="progress" style="height: 15px; width: 80px;">
                                                        <div class="progress-bar bg-success" style="width: {{ $internship->progress }}%"></div>
                                                    </div>
                                                    <small>{{ number_format($internship->progress, 1) }}%</small>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @if($section->internships->count() > 10)
                                        <small class="text-muted">Showing 10 of {{ $section->internships->count() }} students</small>
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-3">
                                    <i class="fas fa-briefcase fa-2x text-muted mb-2"></i>
                                    <p class="text-muted">No students assigned to this section yet.</p>
                                    <a href="{{ route('admin.internships.create') }}" class="btn btn-sm btn-primary">
                                        Assign Internship
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('admin.sections.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <a href="{{ route('admin.sections.edit', $section) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>
        </div>
    </div>
</div>
@endsection