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
                <button type="button" class="btn btn-warning btn-sm me-2" data-bs-toggle="modal" data-bs-target="#bulkAssignModal">
                    <i class="fas fa-layer-group"></i> Bulk Assign
                </button>
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

            <!-- Status Filter Tabs -->
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <a class="nav-link {{ ($status ?? 'all') == 'all' ? 'active' : '' }}" 
                       href="{{ route('teacher.students.index', ['status' => 'all']) }}">
                        <i class="fas fa-users"></i> All Students ({{ $stats['total'] ?? 0 }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ ($status ?? 'all') == 'active' ? 'active' : '' }}" 
                       href="{{ route('teacher.students.index', ['status' => 'active']) }}">
                        <i class="fas fa-play-circle"></i> Active Internships ({{ $stats['active'] ?? 0 }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ ($status ?? 'all') == 'completed' ? 'active' : '' }}" 
                       href="{{ route('teacher.students.index', ['status' => 'completed']) }}">
                        <i class="fas fa-check-circle"></i> Completed ({{ $stats['completed'] ?? 0 }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ ($status ?? 'all') == 'pending' ? 'active' : '' }}" 
                       href="{{ route('teacher.students.index', ['status' => 'pending']) }}">
                        <i class="fas fa-hourglass-half"></i> Pending ({{ $stats['pending'] ?? 0 }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ ($status ?? 'all') == 'no_internship' ? 'active' : '' }}" 
                       href="{{ route('teacher.students.index', ['status' => 'no_internship']) }}">
                        <i class="fas fa-exclamation-triangle"></i> No Internship ({{ $stats['no_internship'] ?? 0 }})
                    </a>
                </li>
            </ul>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            @if(($status ?? 'all') == 'no_internship')
                                <th width="50">
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                </th>
                            @endif
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Course</th>
                            <th>Subject</th>
                            <th>Company</th>
                            <th>Hours</th>
                            <th>Progress</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        @php
                            $internship = $student->internships->first();
                            $hasInternship = $internship ? true : false;
                            $progress = $internship ? $internship->progress : 0;
                            $totalHours = $student->attendances->sum('hours_worked') ?? 0;
                        @endphp
                        <tr>
                            @if(($status ?? 'all') == 'no_internship')
                                <td>
                                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" 
                                           class="form-check-input student-checkbox">
                                </td>
                            @endif
                            <td><strong>{{ $student->student_id ?? 'N/A' }}</strong></td>
                            <td>{{ $student->name }}<br>
                                <small class="text-muted">{{ $student->email }}</small>
                            </td>
                            <td>{{ $student->course ?? 'N/A' }}<br>
                                <small>{{ $student->year_level ?? 'N/A' }} Year</small>
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
                                {{ $internship ? $internship->subject->required_hours : 0 }} hrs
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
                                <a href="{{ route('teacher.students.show', $student) }}" class="btn btn-sm btn-info" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(!$hasInternship)
                                    <button type="button" class="btn btn-sm btn-primary assign-single" 
                                            data-bs-toggle="modal" data-bs-target="#singleAssignModal"
                                            data-student-id="{{ $student->id }}"
                                            data-student-name="{{ $student->name }}"
                                            title="Assign Internship">
                                        <i class="fas fa-briefcase"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="{{ ($status ?? 'all') == 'no_internship' ? '10' : '9' }}" class="text-center py-5">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No students found in this category.</p>
                                    @if(($status ?? 'all') == 'no_internship')
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bulkAssignModal">
                                            <i class="fas fa-layer-group"></i> Bulk Assign Internships
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </div>
            </div>

            <div class="mt-3 d-flex justify-content-between align-items-center">
                <div>
                    Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} of {{ $students->total() ?? 0 }} students
                </div>
                <div>
                    {{ $students->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Assign Modal -->
<div class="modal fade" id="bulkAssignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('teacher.students.bulk-assign') }}">
                @csrf
                <div class="modal-header" style="background-color: #216699; color: white;">
                    <h5 class="modal-title"><i class="fas fa-layer-group"></i> Bulk Assign Internship</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        Selected students will be assigned to the chosen subject.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Selected Students</label>
                        <div id="selectedStudentsList" class="border rounded p-2" style="max-height: 150px; overflow-y: auto;">
                            <p class="text-muted mb-0">No students selected</p>
                        </div>
                    </div>
                    
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
                    <button type="submit" class="btn btn-primary" id="confirmBulkAssign">
                        <i class="fas fa-save"></i> Assign to Selected
                    </button>
                </div>
            </form>
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

@push('scripts')
<script>
    // Select All functionality
    document.getElementById('selectAll')?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.student-checkbox');
        checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        updateSelectedCount();
    });
    
    // Update selected count
    function updateSelectedCount() {
        const checkboxes = document.querySelectorAll('.student-checkbox:checked');
        const count = checkboxes.length;
        const listContainer = document.getElementById('selectedStudentsList');
        
        if (count > 0) {
            let html = '';
            checkboxes.forEach(cb => {
                const row = cb.closest('tr');
                const name = row.cells[2]?.textContent.split('\n')[0] || 'Unknown';
                html += `<div class="mb-1"><small>✓ ${name.trim()}</small></div>`;
            });
            listContainer.innerHTML = html;
        } else {
            listContainer.innerHTML = '<p class="text-muted mb-0">No students selected</p>';
        }
    }
    
    // Update count when checkboxes change
    document.querySelectorAll('.student-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });
    
    // Single assign modal
    document.querySelectorAll('.assign-single').forEach(btn => {
        btn.addEventListener('click', function() {
            const studentId = this.dataset.studentId;
            const studentName = this.dataset.studentName;
            document.getElementById('single_student_id').value = studentId;
            document.getElementById('single_student_name').textContent = studentName;
        });
    });
</script>
@endpush
@endsection