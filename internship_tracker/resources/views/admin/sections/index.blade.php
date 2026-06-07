@extends('layouts.app')

@section('title', 'Manage Sections')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Sections Management</h3>
            <a href="{{ route('admin.sections.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Section
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
                            <th>Section Name</th>
                            <th>Course</th>
                            <th>Year Level</th>
                            <th>Max Students</th>
                            <th>Enrolled</th>
                            <th>Available Slots</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sections as $section)
                        <tr>
                            <td><strong>{{ $section->code }}</strong></td>
                            <td>{{ $section->name }}</td>
                            <td>{{ $section->course }}</td>
                            <td>{{ $section->year_level }} Year</td>
                            <td>{{ $section->max_students }}</td>
                            <td>{{ $section->enrollments_count ?? 0 }}</td>
                            <td>
                                @php
                                    $available = $section->max_students - ($section->enrollments_count ?? 0);
                                @endphp
                                @if($available > 0)
                                    <span class="badge bg-success">{{ $available }} slots</span>
                                @else
                                    <span class="badge bg-danger">Full</span>
                                @endif
                             </td>
                            <td>
                                @if($section->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.sections.show', $section) }}" class="btn btn-sm btn-info">View</a>
                                <a href="{{ route('admin.sections.edit', $section) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('admin.sections.destroy', $section) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this section? All enrolled students will be affected.')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">No sections found. <a href="{{ route('admin.sections.create') }}">Create one</a></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $sections->links() }}
            </div>
        </div>
    </div>
</div>
@endsection