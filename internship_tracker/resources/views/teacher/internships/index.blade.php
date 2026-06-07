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
                            <h3>{{ $stats['total'] ?? 0 }}</h3>
                            <small>Total Internships</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body text-center">
                            <h3>{{ $stats['active'] ?? 0 }}</h3>
                            <small>Active</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info">
                        <div class="card-body text-center">
                            <h3>{{ $stats['completed'] ?? 0 }}</h3>
                            <small>Completed</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body text-center">
                            <h3>{{ $stats['pending'] ?? 0 }}</h3>
                            <small>Pending</small>
                        </div>
                    </div>
                </div>
            </div>

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

            @if(isset($groupedBySection) && count($groupedBySection) > 0)
                <!-- Section Tabs -->
                <ul class="nav nav-tabs mb-3" id="sectionTabs" role="tablist">
                    @foreach($groupedBySection as $sectionName => $internshipsList)
                        @php
                            $tabId = 'section-' . preg_replace('/[^a-zA-Z0-9]/', '-', $sectionName);
                        @endphp
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $loop->first ? 'active' : '' }}" 
                                    data-bs-toggle="tab" 
                                    data-bs-target="#{{ $tabId }}" 
                                    type="button" 
                                    role="tab">
                                <i class="fas fa-users"></i> {{ $sectionName }}
                                <span class="badge bg-secondary ms-1">{{ count($internshipsList) }}</span>
                            </button>
                        </li>
                    @endforeach
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">
                    @foreach($groupedBySection as $sectionName => $internshipsList)
                        @php
                            $tabId = 'section-' . preg_replace('/[^a-zA-Z0-9]/', '-', $sectionName);
                        @endphp
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
                             id="{{ $tabId }}" 
                             role="tabpanel">
                            
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Student</th>
                                            <th>Student ID</th>
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
                                        @foreach($internshipsList as $internship)
                                        <tr>
                                            <td>
                                                {{ $internship->student->name }}<br>
                                                <small class="text-muted">{{ $internship->student->email }}</small>
                                            </td>
                                            <td>{{ $internship->student->student_id ?? 'N/A' }}</p>
                                            <td>
                                                <strong>{{ $internship->subject->code }}</strong><br>
                                                <small class="text-muted">{{ $internship->subject->name }}</small>
                                            </p>
                                            <td>{{ $internship->company_name ?? 'School-based' }}</p>
                                            <td>{{ $internship->start_date ? $internship->start_date->format('M d, Y') : 'N/A' }}</p>
                                            <td>
                                                <strong>{{ number_format($internship->total_hours_rendered, 1) }}</strong> / 
                                                {{ $internship->subject->required_hours }} hrs
                                            </p>
                                            <td>
                                                <div class="progress" style="height: 20px; width: 120px;">
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
                                                <a href="{{ route('teacher.internships.show', $internship) }}" class="btn btn-sm btn-info" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('teacher.internships.edit', $internship) }}" class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                             </p>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No internships assigned yet.</p>
                    <a href="{{ route('teacher.internships.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Assign Your First Internship
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection