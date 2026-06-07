@extends('layouts.app')

@section('title', 'Subject Details')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3>Subject Details: {{ $subject->code }}</h3>
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
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Statistics</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Total Enrolled Students:</strong> {{ $subject->enrollments->count() }}</p>
                            <p><strong>Active Enrollments:</strong> {{ $subject->enrollments->where('status', 'enrolled')->count() }}</p>
                            <p><strong>Completed:</strong> {{ $subject->enrollments->where('status', 'completed')->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary">Back</a>
                <a href="{{ route('admin.subjects.edit', $subject) }}" class="btn btn-warning">Edit</a>
            </div>
        </div>
    </div>
</div>
@endsection