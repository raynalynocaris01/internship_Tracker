{{-- resources/views/teacher/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            {{-- Welcome Card --}}
            <div class="card mb-4">
                <div class="card-header" style="background-color: #216699; color: white;">
                    <h3 class="mb-0">Teacher Dashboard</h3>
                </div>
                <div class="card-body">
                    <p>Welcome back, <strong>{{ Auth::user()->name }}</strong>!</p>
                    <p><strong>Teacher ID:</strong> {{ Auth::user()->teacher_id }}</p>
                    <p><strong>Department:</strong> {{ Auth::user()->department }}</p>
                </div>
            </div>

            {{-- Stats Cards (responsive) --}}
            <div class="row mb-4">
                <div class="col-6 col-md-3 mb-3">
                    <div class="card text-white bg-primary h-100">
                        <div class="card-header py-2 small">Total Students</div>
                        <div class="card-body py-2">
                            <h5 class="card-title mb-0">{{ $totalStudents ?? 0 }}</h5>
                            <p class="card-text small mb-0">All students</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <div class="card text-white bg-success h-100">
                        <div class="card-header py-2 small">Active Internships</div>
                        <div class="card-body py-2">
                            <h5 class="card-title mb-0">{{ $activeInternships ?? 0 }}</h5>
                            <p class="card-text small mb-0">Currently active</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <div class="card text-white bg-info h-100">
                        <div class="card-header py-2 small">Total Hours</div>
                        <div class="card-body py-2">
                            <h5 class="card-title mb-0">{{ $totalHours ?? 0 }}</h5>
                            <p class="card-text small mb-0">Hours completed</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <div class="card text-white bg-warning h-100">
                        <div class="card-header py-2 small">Today's Attendance</div>
                        <div class="card-body py-2">
                            <h5 class="card-title mb-0">{{ $todayAttendance ?? 0 }}</h5>
                            <p class="card-text small mb-0">Logged in today</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Students by Section --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2"
                     style="background-color: #216699; color: white;">
                    <h4 class="mb-0"><i class="fas fa-users"></i> Students by Section</h4>
                    <a href="{{ route('teacher.students.create') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-user-plus"></i> Add New Student
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

                    @if(isset($sections) && $sections->count() > 0)

                        {{-- Section Tabs (scrollable on mobile if many) --}}
                        <ul class="nav nav-tabs mb-3 flex-nowrap overflow-auto" id="sectionTabs" role="tablist" style="white-space: nowrap;">
                            @foreach($sections as $index => $section)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $index == 0 ? 'active' : '' }}"
                                            data-bs-toggle="tab"
                                            data-bs-target="#section-{{ $section->id }}"
                                            type="button" role="tab">
                                        <i class="fas fa-users"></i> {{ $section->name }}
                                        <span class="badge bg-secondary ms-1">{{ $section->students_count ?? 0 }}</span>
                                    </button>
                                </li>
                            @endforeach
                        </ul>

                        <div class="tab-content">
                            @foreach($sections as $index => $section)
                                <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}"
                                     id="section-{{ $section->id }}"
                                     role="tabpanel">

                                    @if($section->students->count() > 0)
                                        {{-- Mobile‑friendly student cards --}}
                                        @foreach($section->students as $student)
                                            @php
                                                $internship        = $student->currentInternship ?? null;
                                                $hasInternship     = $internship !== null;
                                                $progress          = $hasInternship ? $internship->progress : 0;
                                                $totalHoursStudent = $student->attendances?->sum('hours_worked') ?? 0;
                                            @endphp
                                            <div class="card mb-3 shadow-sm">
                                                <div class="card-body p-3">
                                                    {{-- Header: Name + Actions --}}
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div>
                                                            <strong class="fs-6">{{ $student->name }}</strong><br>
                                                            <small class="text-muted">{{ $student->student_id ?? 'N/A' }}</small>
                                                        </div>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="{{ route('teacher.students.show', $student) }}"
                                                               class="btn btn-info" title="View Details">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            @if(!$hasInternship)
                                                                <button type="button"
                                                                        class="btn btn-primary assign-single"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#singleAssignModal"
                                                                        data-student-id="{{ $student->id }}"
                                                                        data-student-name="{{ $student->name }}"
                                                                        title="Assign Internship">
                                                                    <i class="fas fa-briefcase"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    {{-- Details row --}}
                                                    <div class="row g-2 small mb-2">
                                                        <div class="col-6">
                                                            <span class="text-muted">Course:</span> {{ $student->course ?? 'N/A' }}<br>
                                                            <span class="text-muted">Year:</span> {{ $student->year_level ?? 'N/A' }}
                                                        </div>
                                                        <div class="col-6">
                                                            @if($hasInternship)
                                                                <span class="text-muted">Subject:</span> {{ $internship->subject->code ?? 'N/A' }}<br>
                                                                <span class="text-muted">Company:</span> {{ $internship->company_name ?? 'School-based' }}
                                                            @else
                                                                <span class="badge bg-secondary">Not Assigned</span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    {{-- Progress and Hours --}}
                                                    @if($hasInternship)
                                                        <div class="mb-2">
                                                            <div class="d-flex justify-content-between small mb-1">
                                                                <span>Progress</span>
                                                                <span>{{ number_format($progress, 1) }}%</span>
                                                            </div>
                                                            <div class="progress" style="height: 6px;">
                                                                <div class="progress-bar bg-success" style="width: {{ $progress }}%"></div>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <span class="text-muted small">Hours:</span>
                                                                <strong>{{ number_format($totalHoursStudent, 1) }}</strong>
                                                                / {{ $internship->subject->required_hours }} hrs
                                                            </div>
                                                            <div>
                                                                @if($internship->status == 'active')
                                                                    <span class="badge bg-success">Active</span>
                                                                @elseif($internship->status == 'completed')
                                                                    <span class="badge bg-info">Completed</span>
                                                                @elseif($internship->status == 'pending')
                                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                                @else
                                                                    <span class="badge bg-danger">Dropped</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="text-center py-2">
                                                            <span class="badge bg-secondary">No Internship</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No students in this section yet.</p>
                                            <a href="{{ route('teacher.students.create') }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-user-plus"></i> Add Student
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-3">No sections available.</p>
                            <p class="text-muted small">Please contact administrator to create sections.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Single Assign Modal (unchanged) --}}
<div class="modal fade" id="singleAssignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('teacher.students.bulk-assign') }}">
                @csrf
                <input type="hidden" name="student_ids[]" id="single_student_id">
                <div class="modal-header" style="background-color: #216699; color: white;">
                    <h5 class="modal-title"><i class="fas fa-briefcase"></i> Assign Internship</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Assigning internship to: <strong id="single_student_name"></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Subject <span class="text-danger">*</span></label>
                        <select name="subject_id" class="form-control" required>
                            <option value="">Select Subject</option>
                            @foreach($subjects ?? [] as $subject)
                                <option value="{{ $subject->id }}">
                                    {{ $subject->code }} - {{ $subject->name }} ({{ $subject->required_hours }} hrs)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Company Name</label>
                        <input type="text" name="company_name" class="form-control"
                               placeholder="Leave blank for school-based">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Position</label>
                        <input type="text" name="position" class="form-control" placeholder="e.g., Intern">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Internship</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('.assign-single').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('single_student_id').value = this.dataset.studentId;
            document.getElementById('single_student_name').textContent = this.dataset.studentName;
        });
    });
</script>
@endpush
@endsection