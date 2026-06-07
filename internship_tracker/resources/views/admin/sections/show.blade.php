@extends('layouts.app')

@section('title', 'Section Details')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3>Section Details: {{ $section->name }}</h3>
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
                            <th>Current Enrollment</th>
                            <td>{{ $section->enrollments_count ?? 0 }}</td>
                        </tr>
                        <tr>
                            <th>Available Slots</th>
                            <td>
                                @php
                                    $available = $section->max_students - ($section->enrollments_count ?? 0);
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
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Enrolled Students</h5>
                        </div>
                        <div class="card-body">
                            @if($section->enrollments && $section->enrollments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Student Name</th>
                                                <th>Student ID</th>
                                                <th>Subject</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($section->enrollments->take(10) as $enrollment)
                                            <tr>
                                                <td>{{ $enrollment->student->name ?? 'N/A' }}</td>
                                                <td>{{ $enrollment->student->student_id ?? 'N/A' }}</td>
                                                <td>{{ $enrollment->subject->code ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $enrollment->status == 'enrolled' ? 'success' : 'secondary' }}">
                                                        {{ ucfirst($enrollment->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @if($section->enrollments->count() > 10)
                                        <small class="text-muted">Showing 10 of {{ $section->enrollments->count() }} students</small>
                                    @endif
                                </div>
                            @else
                                <p class="text-muted">No students enrolled in this section yet.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('admin.sections.index') }}" class="btn btn-secondary">Back</a>
                <a href="{{ route('admin.sections.edit', $section) }}" class="btn btn-warning">Edit</a>
            </div>
        </div>
    </div>
</div>
@endsection