{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
        <div>
            <h1 class="h2 mb-1 fw-bold text-dark">Admin Dashboard</h1>
            <p class="text-muted mb-0">Welcome back, {{ Auth::user()->name }}! Here's an overview of your internship program.</p>
        </div>
        <div>
            <span class="badge bg-primary fs-6 px-3 py-2 shadow-sm rounded-pill">
                <i class="fas fa-clock me-1"></i> <span id="liveTime">{{ now()->format('h:i:s A') }}</span>
            </span>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100 border-0 border-top border-4 border-primary shadow-sm hover-lift">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stats-icon bg-primary-subtle text-primary rounded-3 p-3">
                            <i class="fas fa-chalkboard-teacher fa-2x"></i>
                        </div>
                        <h3 class="card-title fs-1 fw-bold mb-0 text-dark">{{ $totalTeachers ?? 0 }}</h3>
                    </div>
                    <p class="card-text text-muted small fw-medium mb-3">Total Registered Teachers</p>
                    <a href="{{ route('admin.users.index') }}?role=teacher" class="btn btn-outline-primary btn-sm w-100 rounded-2">
                        Manage Teachers <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100 border-0 border-top border-4 border-success shadow-sm hover-lift">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stats-icon bg-success-subtle text-success rounded-3 p-3">
                            <i class="fas fa-book fa-2x"></i>
                        </div>
                        <h3 class="card-title fs-1 fw-bold mb-0 text-dark">{{ $totalSubjects ?? 0 }}</h3>
                    </div>
                    <p class="card-text text-muted small fw-medium mb-3">Active Program Subjects</p>
                    <a href="{{ route('admin.subjects.index') }}" class="btn btn-outline-success btn-sm w-100 rounded-2">
                        Manage Subjects <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card h-100 border-0 border-top border-4 border-info shadow-sm hover-lift">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stats-icon bg-info-subtle text-info rounded-3 p-3">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <h3 class="card-title fs-1 fw-bold mb-0 text-dark">{{ $totalSections ?? 0 }}</h3>
                    </div>
                    <p class="card-text text-muted small fw-medium mb-3">Monitored Class Sections</p>
                    <a href="{{ route('admin.sections.index') }}" class="btn btn-outline-info btn-sm w-100 rounded-2">
                        Manage Sections <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card h-100 border-0 border-top border-4 border-warning shadow-sm hover-lift">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stats-icon bg-warning-subtle text-warning rounded-3 p-3">
                            <i class="fas fa-graduation-cap fa-2x"></i>
                        </div>
                        <h3 class="card-title fs-1 fw-bold mb-0 text-dark">{{ $totalStudents ?? 0 }}</h3>
                    </div>
                    <p class="card-text text-muted small fw-medium mb-3">Enrolled Interns / Students</p>
                    <a href="{{ route('admin.users.index') }}?role=student" class="btn btn-outline-warning btn-sm w-100 rounded-2">
                        View All Students <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-5">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-2">
            <h5 class="mb-0 fw-bold"><i class="fas fa-bolt me-2 text-primary"></i>Quick Actions</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ route('admin.users.create') }}?role=teacher" class="action-chip d-block text-center py-3 px-2">
                        <i class="fas fa-user-plus d-block fs-4 mb-2 text-primary"></i>
                        <small>Add Teacher</small>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ route('admin.subjects.create') }}" class="action-chip d-block text-center py-3 px-2">
                        <i class="fas fa-folder-plus d-block fs-4 mb-2 text-success"></i>
                        <small>Add Subject</small>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ route('admin.sections.create') }}" class="action-chip d-block text-center py-3 px-2">
                        <i class="fas fa-plus-square d-block fs-4 mb-2 text-info"></i>
                        <small>Add Section</small>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ route('admin.users.create') }}?role=student" class="action-chip d-block text-center py-3 px-2">
                        <i class="fas fa-user-graduate d-block fs-4 mb-2 text-warning"></i>
                        <small>Add Student</small>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ route('admin.internships.create') }}" class="action-chip d-block text-center py-3 px-2">
                        <i class="fas fa-file-signature d-block fs-4 mb-2 text-danger"></i>
                        <small>Assign Internship</small>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ route('admin.attendances.index') }}" class="action-chip d-block text-center py-3 px-2">
                        <i class="fas fa-chart-bar d-block fs-4 mb-2 text-secondary"></i>
                        <small>View Reports</small>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-chart-pie me-2 text-primary"></i>Top Subjects by Internships</h5>
                    <a href="{{ route('admin.subjects.index') }}" class="text-primary small text-decoration-none">Manage →</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small uppercase">
                                <tr>
                                    <th class="ps-4">Subject</th>
                                    <th class="text-center">Active</th>
                                    <th class="pe-4">Completion</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topSubjects ?? [] as $subject)
                                <tr>
                                    <td class="fw-semibold ps-4 text-dark">{{ is_array($subject) ? $subject['name'] : $subject->name }}</td>
                                    <td class="text-center fw-bold">{{ is_array($subject) ? $subject['student_count'] : $subject->student_count }}</td>
                                    <td class="pe-4" style="width: 45%">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1 bg-light" style="height: 6px;">
                                                <div class="progress-bar bg-primary rounded-pill" style="width: {{ is_array($subject) ? $subject['completion_rate'] : $subject->completion_rate }}%"></div>
                                            </div>
                                            <span class="small fw-semibold text-secondary text-nowrap">{{ is_array($subject) ? $subject['completion_rate'] : $subject->completion_rate }}%</span>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-5">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block text-black-50"></i> No data available
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-user-clock me-2 text-primary"></i>Recently Added Students</h5>
                    <a href="{{ route('admin.users.index') }}?role=student" class="text-primary small text-decoration-none">View all →</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small uppercase">
                                <tr>
                                    <th class="ps-4">Student Name</th>
                                    <th>ID</th>
                                    <th>Course</th>
                                    <th class="pe-4">Date Added</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentStudents ?? [] as $student)
                                <tr>
                                    <td class="fw-semibold ps-4 text-dark">{{ $student->name }}</td>
                                    <td class="text-secondary">{{ $student->student_id }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $student->course }}</span></td>
                                    <td class="text-muted pe-4 small">{{ $student->created_at->format('M d, Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5">
                                        <i class="fas fa-users-slash fa-2x mb-2 d-block text-black-50"></i> No students added yet
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Premium Hover & Elevation Effects */
    .hover-lift {
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .hover-lift:hover {
        transform: translateY(-4px);
        box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.08) !important;
    }
    
    /* Stats Accent background colors (Fallback helpers for Bootstrap 5 variants) */
    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1); }
    .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1); }
    .bg-info-subtle    { background-color: rgba(13, 202, 240, 0.1); }
    .bg-warning-subtle { background-color: rgba(255, 193, 7, 0.1); }

    /* Action Grid Design */
    .action-chip {
        background: #ffffff;
        border-radius: 0.75rem;
        color: #495057;
        text-decoration: none;
        transition: all 0.2s ease-in-out;
        font-weight: 500;
        border: 1px solid #dee2e6;
    }
    .action-chip:hover {
        background: #f8f9fa;
        color: #0d6efd;
        transform: translateY(-2px);
        border-color: #0d6efd;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.05);
    }
    
    /* Table Enhancements */
    .table th {
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        font-weight: 700;
    }
    .table td {
        padding: 0.85rem 0.75rem;
    }
    
    /* Precise Monospaced Alignment for Clock */
    #liveTime {
        font-family: 'SFMono-Regular', Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    
    @media (max-width: 768px) {
        .fs-1 { font-size: 1.75rem !important; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        function updateLiveTime() {
            const now = new Date();
            const options = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
            const timeString = now.toLocaleTimeString('en-US', options);
            const timeElement = document.getElementById('liveTime');
            if (timeElement) timeElement.textContent = timeString;
        }
        setInterval(updateLiveTime, 1000);
    });
</script>
@endsection