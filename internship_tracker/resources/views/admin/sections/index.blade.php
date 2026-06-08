@extends('layouts.app')

@section('title', 'Manage Sections')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #216699; color: white;">
            <h4 class="mb-0">
                <i class="fas fa-users"></i> Sections Management
            </h4>
            <a href="{{ route('admin.sections.create') }}" class="btn btn-light btn-sm">
                <i class="fas fa-plus"></i> Add New Section
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
                            <!-- <th>Code</th> -->
                            <th>Section Name</th>
                            <th>Course</th>
                            <th>Year Level</th>
                            <th>Max Students</th>
                            <th>Active Internships</th>  <!-- Changed from "Enrolled" -->
                            <th>Available Slots</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sections as $section)
                        <tr>
                            <!-- <td><strong>{{ $section->code }}</strong></td> -->
                            <td>{{ $section->name }}</td>
                            <td>{{ $section->course }}</td>
                            <td>{{ $section->year_level }} Year</td>
                            <td class="text-center">{{ $section->max_students }}</td>
                            <td class="text-center">
                                <span class="badge bg-primary">{{ $section->internships_count ?? 0 }}</span>  <!-- Changed from enrollments_count -->
                            </td>
                            <td>
                                @php
                                    $available = $section->max_students - ($section->internships_count ?? 0);
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
                                <a href="{{ route('admin.sections.show', $section) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('admin.sections.edit', $section) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.sections.destroy', $section) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this section? All assigned internships will be affected.')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">
                                    <i class="fas fa-users fa-2x mb-2"></i>
                                    <p>No sections found.</p>
                                    <a href="{{ route('admin.sections.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Create your first section
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3 d-flex justify-content-between align-items-center">
                <div>
                    Showing {{ $sections->firstItem() ?? 0 }} to {{ $sections->lastItem() ?? 0 }} of {{ $sections->total() ?? 0 }} sections
                </div>
                <div>
                    {{ $sections->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection