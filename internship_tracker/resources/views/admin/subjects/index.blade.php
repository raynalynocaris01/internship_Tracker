@extends('layouts.app')

@section('title', 'Manage Subjects')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #216699; color: white;">
            <h4 class="mb-0">
                <i class="fas fa-book"></i> Subjects Management
            </h4>
            <a href="{{ route('admin.subjects.create') }}" class="btn btn-light btn-sm">
                <i class="fas fa-plus"></i> Add New Subject
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
                            <th>Code</th>
                            <th>Subject Name</th>
                            <th>Units</th>
                            <th>Required Hours</th>
                            <th>Semester</th>
                            <th>School Year</th>
                            <th>Active Internships</th>  <!-- Changed from "Enrolled" -->
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subjects as $subject)
                        <tr>
                            <td><strong>{{ $subject->code }}</strong></td>
                            <td>{{ $subject->name }}</td>
                            <td class="text-center">{{ $subject->units }}</td>
                            <td class="text-center">{{ number_format($subject->required_hours) }} hrs</td>
                            <td class="text-center">{{ $subject->semester }} Sem</td>
                            <td class="text-center">{{ $subject->school_year }}</td>
                            <td class="text-center">
                                <span class="badge bg-primary">{{ $subject->internships_count ?? 0 }}</span>  <!-- Changed from enrollments_count -->
                            </td>
                            <td class="text-center">
                                @if($subject->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.subjects.show', $subject) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('admin.subjects.edit', $subject) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this subject? This will also delete all associated internships.')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">
                                    <i class="fas fa-book fa-2x mb-2"></i>
                                    <p>No subjects found.</p>
                                    <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Create your first subject
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3 d-flex justify-content-between align-items-center">
                <div>
                    Showing {{ $subjects->firstItem() ?? 0 }} to {{ $subjects->lastItem() ?? 0 }} of {{ $subjects->total() ?? 0 }} subjects
                </div>
                <div>
                    {{ $subjects->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection