{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container">
    <!-- Page Header -->
    <div class="page-header">
        <h1>Admin Dashboard</h1>
        <p>Welcome back, {{ Auth::user()->name }}! Here's an overview of your internship program.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-4">
        <div class="card dashboard-card">
            <div class="dashboard-card-icon">👨‍🏫</div>
            <h3>Teachers</h3>
            <div class="count">{{ $totalTeachers ?? 0 }}</div>
            <a href="{{ route('admin.users.index') }}?role=teacher" class="btn btn-sm">Manage →</a>
        </div>

        <div class="card dashboard-card">
            <div class="dashboard-card-icon">📚</div>
            <h3>Subjects</h3>
            <div class="count">{{ $totalSubjects ?? 0 }}</div>
            <a href="{{ route('admin.subjects.index') }}" class="btn btn-sm">Manage →</a>
        </div>

        <div class="card dashboard-card">
            <div class="dashboard-card-icon">👥</div>
            <h3>Sections</h3>
            <div class="count">{{ $totalSections ?? 0 }}</div>
            <a href="{{ route('admin.sections.index') }}" class="btn btn-sm">Manage →</a>
        </div>

        <div class="card dashboard-card">
            <div class="dashboard-card-icon">🎓</div>
            <h3>Students</h3>
            <div class="count">{{ $totalStudents ?? 0 }}</div>
            <a href="{{ route('admin.users.index') }}?role=student" class="btn btn-sm">View All →</a>
        </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="card quick-actions" style="margin-top: 24px;">
        <h2 style="margin-bottom: 20px; font-size: 1.3rem;">Quick Actions</h2>
        <div class="grid grid-6">
            <a href="{{ route('admin.users.create') }}?role=teacher" class="action-chip">
                <span>➕</span> Add Teacher
            </a>
            <a href="{{ route('admin.subjects.create') }}" class="action-chip">
                <span>📖</span> Add Subject
            </a>
            <a href="{{ route('admin.sections.create') }}" class="action-chip">
                <span>🏫</span> Add Section
            </a>
            <a href="{{ route('admin.users.create') }}?role=student" class="action-chip">
                <span>👨‍🎓</span> Add Student
            </a>
            <a href="{{ route('admin.internships.create') }}" class="action-chip">
                <span>📋</span> Assign Internship
            </a>
            <a href="{{ route('admin.attendances.index') }}" class="action-chip">
                <span>📊</span> View Reports
            </a>
        </div>
    </div>

    <!-- Two Column Layout for Recent Activity & System Status -->
    <div class="grid grid-2" style="margin-top: 24px;">
        <!-- Recent Activity -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="font-size: 1.2rem;">Recent Activity</h2>
                <a href="#" class="text-muted" style="font-size: 0.85rem;">View all →</a>
            </div>
            <div class="activity-list">
                @forelse($recentActivities ?? [] as $activity)
                    <div class="activity-item">
                        <div class="activity-dot"></div>
                        <div>
                            <p style="margin: 0 0 4px;">{{ is_array($activity) ? $activity['description'] : $activity->description }}</p>
                            <span class="text-muted" style="font-size: 0.75rem;">{{ is_array($activity) ? $activity['time'] : $activity->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <div class="activity-item">
                        <div class="activity-dot"></div>
                        <div>
                            <p style="margin: 0 0 4px;">Welcome to your dashboard!</p>
                            <span class="text-muted" style="font-size: 0.75rem;">Just now</span>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot"></div>
                        <div>
                            <p style="margin: 0 0 4px;">System is ready and running</p>
                            <span class="text-muted" style="font-size: 0.75rem;">Today</span>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot"></div>
                        <div>
                            <p style="margin: 0 0 4px;">You can start adding teachers and subjects</p>
                            <span class="text-muted" style="font-size: 0.75rem;">Now</span>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- System Status -->
        <div class="card">
            <h2 style="font-size: 1.2rem; margin-bottom: 20px;">System Status</h2>
            <div class="status-list">
                <div class="status-item">
                    <span>Database Connection</span>
                    <span class="status-badge online">● Online</span>
                </div>
                <div class="status-item">
                    <span>Server Software</span>
                    <span>{{ $_SERVER['SERVER_SOFTWARE'] ?? 'Laravel Development Server' }}</span>
                </div>
                <div class="status-item">
                    <span>PHP Version</span>
                    <span>{{ PHP_VERSION }}</span>
                </div>
                <div class="status-item">
                    <span>Laravel Version</span>
                    <span>{{ app()->version() }}</span>
                </div>
                <div class="status-item">
                    <span>Active Internships</span>
                    <span>{{ $totalActive ?? 0 }}</span>  <!-- Changed from totalEnrolled -->
                </div>
                <div class="status-item">
                    <span>Completed Internships</span>
                    <span>{{ $totalCompleted ?? 0 }}</span>  <!-- Added -->
                </div>
                <div class="status-item">
                    <span>Current Time</span>
                    <span id="liveTime">{{ date('h:i:s A') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables Section -->
    <div class="grid grid-2" style="margin-top: 24px;">
        <!-- Top Subjects -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="font-size: 1.2rem;">Top Subjects by Internships</h2>  <!-- Changed from "Enrollment" -->
                <a href="{{ route('admin.subjects.index') }}" class="text-muted" style="font-size: 0.85rem;">Manage →</a>
            </div>
            <div class="table-responsive">
                <table style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Active Internships</th>  <!-- Changed from "Students" -->
                            <th>Completion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topSubjects ?? [] as $subject)
                        <tr>
                            <td>{{ is_array($subject) ? $subject['name'] : $subject->name }}</td>
                            <td>{{ is_array($subject) ? $subject['student_count'] : $subject->student_count }}</p>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="flex: 1; background: #e5e7eb; border-radius: 999px; height: 6px; overflow: hidden;">
                                        <div style="width: {{ is_array($subject) ? $subject['completion_rate'] : $subject->completion_rate }}%; background: #216699; height: 100%;"></div>
                                    </div>
                                    <span style="font-size: 0.8rem;">{{ is_array($subject) ? $subject['completion_rate'] : $subject->completion_rate }}%</span>
                                </div>
                            </p>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted" style="padding: 40px;">No data available</p>
                            </tr>
                        @endforelse
                    </tbody>
                </div>
            </div>
        </div>

        <!-- Recent Students -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="font-size: 1.2rem;">Recently Added Students</h2>
                <a href="{{ route('admin.users.index') }}?role=student" class="text-muted" style="font-size: 0.85rem;">View all →</a>
            </div>
            <div class="table-responsive">
                <table style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Student ID</th>
                            <th>Course</th>
                            <th>Date Added</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentStudents ?? [] as $student)
                        <tr>
                            <td>{{ $student->name }}</p>
                            <td>{{ $student->student_id }}</p>
                            <td>{{ $student->course }}</p>
                            <td>{{ $student->created_at->format('M d, Y') }}</p>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted" style="padding: 40px;">No students added yet</p>
                            </tr>
                        @endforelse
                    </tbody>
                </div>
            </div>
        </div>
    </div>
</div>


<style>
/* Dashboard Specific Styles */
.grid-4 {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

.grid-6 {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 12px;
}

.grid-2 {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.dashboard-card {
    text-align: center;
    transition: all 0.2s ease;
    padding: 20px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.dashboard-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.dashboard-card-icon {
    font-size: 2.5rem;
    margin-bottom: 12px;
}

.dashboard-card h3 {
    font-size: 0.9rem;
    font-weight: 600;
    color: #6b7280;
    margin: 0 0 8px;
    text-transform: uppercase;
}

.dashboard-card .count {
    font-size: 2.5rem;
    font-weight: 700;
    color: #216699;
    margin: 8px 0;
}

.action-chip {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 16px;
    background: #f3f4f6;
    border-radius: 40px;
    color: #374151;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.2s ease;
    text-decoration: none;
}

.action-chip:hover {
    background: #216699;
    color: white;
    transform: translateY(-2px);
    text-decoration: none;
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.activity-dot {
    width: 8px;
    height: 8px;
    background: #216699;
    border-radius: 50%;
    margin-top: 8px;
    flex-shrink: 0;
}

.status-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.status-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #e5e7eb;
}

.status-item:last-child {
    border-bottom: none;
}

.status-badge.online {
    background: #ecfdf5;
    color: #166534;
    padding: 4px 12px;
    border-radius: 40px;
    font-size: 0.75rem;
}

.text-muted {
    color: #6b7280;
}

.table-responsive {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 12px 8px;
    border-bottom: 1px solid #e5e7eb;
    text-align: left;
}

.quick-actions {
    background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
    padding: 20px;
    border-radius: 10px;
}

.btn-sm {
    display: inline-block;
    padding: 6px 12px;
    font-size: 0.8rem;
    background: #216699;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    transition: background 0.2s;
}

.btn-sm:hover {
    background: #174b73;
    text-decoration: none;
    color: white;
}

@media (max-width: 1024px) {
    .grid-4 { grid-template-columns: repeat(2, 1fr); }
    .grid-6 { grid-template-columns: repeat(3, 1fr); }
    .grid-2 { grid-template-columns: 1fr; }
}

@media (max-width: 768px) {
    .grid-4, .grid-6 { grid-template-columns: 1fr; }
}
</style>

<script>
function updateLiveTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('en-US', { 
        hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true 
    });
    const timeElement = document.getElementById('liveTime');
    if (timeElement) timeElement.textContent = timeString;
}
setInterval(updateLiveTime, 1000);
</script>
@endsection