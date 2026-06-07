@extends('layouts.app')

@section('title', 'Internship Management')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #216699; color: white;">
            <h4 class="mb-0">
                <i class="fas fa-briefcase"></i> Internship Management
            </h4>
            <a href="{{ route('admin.internships.create') }}" class="btn btn-light btn-sm">
                <i class="fas fa-plus"></i> Assign Internship
            </a>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Student</th>
                            <th>Student ID</th>
                            <th>Subject</th>
                            <th>Teacher</th>
                            <th>Section</th>
                            <th>Company</th>
                            <th>Status</th>
                            <th>Hours</th>
                            <th>Progress</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($internships as $internship)
                        <tr>
                            <td>{{ $internship->id }}</td>
                            <td>{{ $internship->student->name ?? 'N/A' }}</td>
                            <td>{{ $internship->student->student_id ?? 'N/A' }}</td>
                            <td>{{ $internship->subject->code ?? 'N/A' }} - {{ $internship->subject->name ?? 'N/A' }}</td>
                            <td>{{ $internship->teacher->name ?? 'Not Assigned' }}</td>
                            <td>{{ $internship->section->name ?? 'N/A' }}</td>
                            <td>{{ $internship->company_name ?? 'School-based' }}</td>  <!-- New column -->
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
                                {{ number_format($internship->total_hours_rendered, 1) }} / 
                                {{ $internship->subject->required_hours ?? 0 }} hrs
                            </td>
                            <td>
                                @php
                                    $progress = $internship->progress;
                                @endphp
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-success" style="width: {{ $progress }}%">
                                        {{ number_format($progress, 1) }}%
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('admin.internships.show', $internship) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('admin.internships.edit', $internship) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.internships.destroy', $internship) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this internship? This will also delete all attendance records.')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted">
                                    No internships found. <a href="{{ route('admin.internships.create') }}">Assign your first internship</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $internships->links() }}
            </div>
        </div>
    </div>
</div>
@endsection