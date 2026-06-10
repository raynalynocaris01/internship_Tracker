@extends('layouts.app')

@section('title', 'Manage Sections')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2"
             style="background-color: #216699; color: white;">
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

            @if($sections->count() > 0)
                {{-- Mobile‑friendly section cards --}}
                @foreach($sections as $section)
                    @php
                        $available = $section->max_students - ($section->internships_count ?? 0);
                    @endphp
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body p-3">
                            {{-- Header: Section name + status + actions --}}
                            <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-2">
                                <div>
                                    <h5 class="mb-0">{{ $section->name }}</h5>
                                    <small class="text-muted">{{ $section->course }} - Year {{ $section->year_level }}</small>
                                </div>
                                <div class="d-flex gap-2">
                                    @if($section->status == 'active')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.sections.show', $section) }}" class="btn btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.sections.edit', $section) }}" class="btn btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.sections.destroy', $section) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this section? All assigned internships will be affected.')" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Details row (3 columns on mobile, 4 on larger) --}}
                            <div class="row g-2 mt-2 small">
                                <div class="col-6 col-md-3">
                                    <span class="text-muted">Max Students</span><br>
                                    <strong>{{ $section->max_students }}</strong>
                                </div>
                                <div class="col-6 col-md-3">
                                    <span class="text-muted">Active Internships</span><br>
                                    <span class="badge bg-primary">{{ $section->internships_count ?? 0 }}</span>
                                </div>
                                <div class="col-6 col-md-3">
                                    <span class="text-muted">Available Slots</span><br>
                                    @if($available > 0)
                                        <span class="badge bg-success">{{ $available }} slots</span>
                                    @else
                                        <span class="badge bg-danger">Full</span>
                                    @endif
                                </div>
                                <div class="col-6 col-md-3">
                                    <span class="text-muted">Created</span><br>
                                    <span class="small">{{ $section->created_at ? $section->created_at->format('M d, Y') : 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Pagination --}}
                <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="small text-muted">
                        Showing {{ $sections->firstItem() ?? 0 }} to {{ $sections->lastItem() ?? 0 }} 
                        of {{ $sections->total() ?? 0 }} sections
                    </div>
                    <div>
                        {{ $sections->links() }}
                    </div>
                </div>

            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No sections found.</p>
                    <a href="{{ route('admin.sections.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create your first section
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection