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

            {{-- Stats Cards --}}
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-header">Total Students</div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $totalStudents ?? 0 }}</h5>
                            <p class="card-text">All students</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-header">Active Internships</div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $activeInternships ?? 0 }}</h5>
                            <p class="card-text">Currently active</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info mb-3">
                        <div class="card-header">Total Hours</div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $totalHours ?? 0 }}</h5>
                            <p class="card-text">Hours completed</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning mb-3">
                        <div class="card-header">Today's Attendance</div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $todayAttendance ?? 0 }}</h5>
                            <p class="card-text">Logged in today</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Students by Section --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center"
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

                        <ul class="nav nav-tabs mb-3" id="sectionTabs" role="tablist">
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
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Student ID</th>
                                                        <th>Name</th>
                                                        <th>Course</th>
                                                        <th>Subject</th>
                                                        <th>Company</th>
                                                        <th>Progress</th>
                                                        <th>Total Hours</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($section->students as $student)
                                                    @php
                                                        // Use currentInternship set by controller, NOT $student->internships->first()
                                                        $internship        = $student->currentInternship ?? null;
                                                        $hasInternship     = $internship !== null;
                                                        $progress          = $hasInternship ? $internship->progress : 0;
                                                        $totalHoursStudent = $student->attendances?->sum('hours_worked') ?? 0;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $student->student_id ?? 'N/A' }}</td>
                                                        <td>
                                                            {{ $student->name }}<br>
                                                            <small class="text-muted">{{ $student->email }}</small>
                                                        </td>
                                                        <td>
                                                            {{ $student->course ?? 'N/A' }}<br>
                                                            <small>Year {{ $student->year_level ?? 'N/A' }}</small>
                                                        </td>
                                                        <td>
                                                            @if($hasInternship)
                                                                <strong>{{ $internship->subject->code ?? 'N/A' }}</strong><br>
                                                                <small class="text-muted">{{ $internship->subject->name ?? '' }}</small>
                                                            @else
                                                                <span class="badge bg-secondary">Not Assigned</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($hasInternship)
                                                                {{ $internship->company_name ?? 'School-based' }}
                                                                @if($internship->position)
                                                                    <br><small class="text-muted">{{ $internship->position }}</small>
                                                                @endif
                                                            @else
                                                                —
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($hasInternship)
                                                                <div class="progress" style="height: 6px; width: 100px; display: inline-block;">
                                                                    <div class="progress-bar bg-success" style="width: {{ $progress }}%"></div>
                                                                </div>
                                                                <span class="ms-1">{{ number_format($progress, 1) }}%</span>
                                                            @else
                                                                <span class="text-muted">—</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <strong>{{ number_format($totalHoursStudent, 1) }}</strong> /
                                                            {{ $hasInternship ? $internship->subject->required_hours : 0 }} hrs
                                                        </td>
                                                        <td>
                                                            @if($hasInternship)
                                                                @if($internship->status == 'active')
                                                                    <span class="badge bg-success">Active</span>
                                                                @elseif($internship->status == 'completed')
                                                                    <span class="badge bg-info">Completed</span>
                                                                @elseif($internship->status == 'pending')
                                                                    <span class="badge bg-warning">Pending</span>
                                                                @else
                                                                    <span class="badge bg-danger">Dropped</span>
                                                                @endif
                                                            @else
                                                                <span class="badge bg-secondary">No Internship</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('teacher.students.show', $student) }}"
                                                               class="btn btn-sm btn-info" title="View Details">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            @if(!$hasInternship)
                                                                <button type="button"
                                                                        class="btn btn-sm btn-primary assign-single"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#singleAssignModal"
                                                                        data-student-id="{{ $student->id }}"
                                                                        data-student-name="{{ $student->name }}"
                                                                        title="Assign Internship">
                                                                    <i class="fas fa-briefcase"></i>
                                                                </button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
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

{{-- Single Assign Modal --}}
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