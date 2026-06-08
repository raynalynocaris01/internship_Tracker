@extends('layouts.app')

@section('title', 'Student Management')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #216699; color: white;">
            <h3 class="mb-0">
                <i class="fas fa-users"></i> Student Management
            </h3>
            <div>
                <a href="{{ route('teacher.students.create') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-user-plus"></i> Add New Student
                </a>
            </div>
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

            @if(isset($sections) && count($sections) > 0)
                <!-- Section Tabs -->
                <ul class="nav nav-tabs mb-3" id="sectionTabs" role="tablist">
                    @foreach($sections as $index => $section)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $index == 0 ? 'active' : '' }}"
                                    data-bs-toggle="tab"
                                    data-bs-target="#section-{{ Str::slug($section->name) }}"
                                    type="button"
                                    role="tab">
                                <i class="fas fa-users"></i> {{ $section->name }}
                                <span class="badge bg-secondary ms-1">{{ $section->students_count ?? 0 }}</span>
                            </button>
                        </li>
                    @endforeach
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">
                    @foreach($sections as $index => $section)
                        <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}"
                             id="section-{{ Str::slug($section->name) }}"
                             role="tabpanel">

                            @if($section->students && $section->students->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Student ID</th>
                                                <th>Name</th>
                                                <th>Course</th>
                                                <th>Year Level</th>
                                                <th>Subject</th>
                                                <th>Company</th>
                                                <th>Hours</th>
                                                <th>Progress</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($section->students as $student)
                                            @php
                                                $internship    = $student->internship ?? null;
                                                $hasInternship = $internship ? true : false;
                                                $progress      = $internship ? $internship->progress : 0;
                                                // ✅ FIX: use already eager-loaded attendances relationship
                                                $totalHours    = $student->attendances ? $student->attendances->sum('hours_worked') : 0;
                                            @endphp
                                            <tr>
                                                <td><strong>{{ $student->student_id ?? 'N/A' }}</strong></td>
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
                                                    <strong>{{ number_format($totalHours, 1) }}</strong> /
                                                    {{ $hasInternship ? $internship->subject->required_hours : 0 }} hrs
                                                </td>
                                                <td>
                                                    @if($hasInternship)
                                                        <div class="progress" style="height: 20px; width: 100px;">
                                                            <div class="progress-bar bg-success" style="width: {{ $progress }}%">
                                                                {{ number_format($progress, 1) }}%
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
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
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="{{ route('teacher.students.show', $student) }}" class="btn btn-info" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                        
                                                        @if(!$hasInternship)
                                                            <button type="button" class="btn btn-primary assign-single"
                                                                    data-bs-toggle="modal" data-bs-target="#singleAssignModal"
                                                                    data-student-id="{{ $student->id }}"
                                                                    data-student-name="{{ $student->name }}"
                                                                    title="Assign Internship">
                                                                <i class="fas fa-briefcase"></i>
                                                            </button>
                                                        @endif
                                                        <button type="button" class="btn btn-danger delete-student"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#deleteStudentModal"
                                                                data-student-id="{{ $student->id }}"
                                                                data-student-name="{{ $student->name }}"
                                                                title="Delete Student">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
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
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-3">No students assigned to you yet.</p>
                    <a href="{{ route('teacher.students.create') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Add Your First Student
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Single Assign Modal -->
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
                        <input type="text" name="company_name" class="form-control" placeholder="Leave blank for school-based">
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

<!-- Delete Student Modal -->
<div class="modal fade" id="deleteStudentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="" id="deleteStudentForm">
                @csrf
                @method('DELETE')
                <div class="modal-header" style="background-color: #dc3545; color: white;">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Delete Student</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the student: <strong id="delete_student_name"></strong>?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-warning"></i>
                        <strong>Warning:</strong> This action will permanently delete:
                        <ul class="mb-0 mt-2">
                            <li>The student account</li>
                            <li>All attendance records for this student</li>
                            <li>The internship assignment (if any)</li>
                            <li>The student's QR code</li>
                        </ul>
                    </div>
                    <p class="text-muted mt-2">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Permanently</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Single assign modal
    document.querySelectorAll('.assign-single').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('single_student_id').value = this.dataset.studentId;
            document.getElementById('single_student_name').textContent = this.dataset.studentName;
        });
    });

    // Delete student modal
    document.querySelectorAll('.delete-student').forEach(btn => {
        btn.addEventListener('click', function() {
            const form = document.getElementById('deleteStudentForm');
            form.action = '/teacher/students/' + this.dataset.studentId;
            document.getElementById('delete_student_name').textContent = this.dataset.studentName;
        });
    });
</script>
@endpush
@endsection