@extends('layouts.app')

@section('title', 'Internship Management')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #216699; color: white;">
            <h4 class="mb-0">
                <i class="fas fa-briefcase"></i> Internship Management
            </h4>
            <a href="{{ route('teacher.internships.create') }}" class="btn btn-light btn-sm">
                <i class="fas fa-plus"></i> Assign Internship
            </a>
        </div>

        <div class="card-body">
            <!-- Stats Row -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body text-center">
                            <h3>{{ $stats['total'] }}</h3>
                            <small>Total Internships</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body text-center">
                            <h3>{{ $stats['active'] }}</h3>
                            <small>Active</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info">
                        <div class="card-body text-center">
                            <h3>{{ $stats['completed'] }}</h3>
                            <small>Completed</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body text-center">
                            <h3>{{ $stats['pending'] }}</h3>
                            <small>Pending</small>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Subject</th>
                            <th>Company</th>
                            <th>Start Date</th>
                            <th>Hours</th>
                            <th>Progress</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($internships as $internship)
                        <tr>
                            <td>{{ $internship->student->name }}<br>
                                <small class="text-muted">{{ $internship->student->student_id }}</small>
                            </td>
                            <td>{{ $internship->subject->code }}<br>
                                <small class="text-muted">{{ $internship->subject->name }}</small>
                            </td>
                            <td>{{ $internship->company_name ?? 'School-based' }}</p>
                            <td>{{ $internship->start_date->format('M d, Y') }}</p>
                            <td>{{ number_format($internship->total_hours_rendered, 1) }} / {{ $internship->subject->required_hours }} hrs</p>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-success" style="width: {{ $internship->progress }}%">
                                        {{ number_format($internship->progress, 1) }}%
                                    </div>
                                </div>
                            </p>
                            <td>
                                <span class="badge bg-{{ $internship->status_badge }}">
                                    {{ $internship->status_label }}
                                </span>
                            </p>
                            <td>
                                <a href="{{ route('teacher.internships.show', $internship) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('teacher.internships.edit', $internship) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                             </p>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No internships assigned yet.</p>
                            </tr>
                        @endforelse
                    </tbody>
                </div>
            </div>
            {{ $internships->links() }}
        </div>
    </div>
</div>
@endsection