@extends('layouts.app')

@section('title', 'Manage Subjects')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Subjects Management</h3>
            <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Subject
            </a>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Subject Name</th>
                            <th>Units</th>
                            <th>Required Hours</th>
                            <th>Semester</th>
                            <th>School Year</th>
                            <th>Enrolled</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subjects as $subject)
                        <tr>
                            <td><strong>{{ $subject->code }}</strong></td>
                            <td>{{ $subject->name }}</td>
                            <td>{{ $subject->units }}</td>
                            <td>{{ number_format($subject->required_hours) }} hrs</td>
                            <td>{{ $subject->semester }} Sem</td>
                            <td>{{ $subject->school_year }}</td>
                            <td>{{ $subject->enrollments_count ?? 0 }} students</td>
                            <td>
                                @if($subject->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.subjects.show', $subject) }}" class="btn btn-sm btn-info">View</a>
                                <a href="{{ route('admin.subjects.edit', $subject) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this subject?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">No subjects found. <a href="{{ route('admin.subjects.create') }}">Create one</a></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $subjects->links() }}
            </div>
        </div>
    </div>
</div>
@endsection