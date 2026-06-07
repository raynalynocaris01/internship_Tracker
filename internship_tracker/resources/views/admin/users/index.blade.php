@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>User Management</h3>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New User
            </a>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Role Filter Tabs -->
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <a class="nav-link {{ ($role ?? 'all') == 'all' ? 'active' : '' }}" href="{{ route('admin.users.index', ['role' => 'all']) }}">
                        All Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ ($role ?? 'all') == 'student' ? 'active' : '' }}" href="{{ route('admin.users.index', ['role' => 'student']) }}">
                        Students ({{ $totalStudents ?? 0 }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ ($role ?? 'all') == 'teacher' ? 'active' : '' }}" href="{{ route('admin.users.index', ['role' => 'teacher']) }}">
                        Teachers ({{ $totalTeachers ?? 0 }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ ($role ?? 'all') == 'admin' ? 'active' : '' }}" href="{{ route('admin.users.index', ['role' => 'admin']) }}">
                        Admins ({{ $totalAdmins ?? 0 }})
                    </a>
                </li>
            </ul>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>ID Number</th>
                            <th>Department/Course</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role == 'admin')
                                    <span class="badge bg-danger">Admin</span>
                                @elseif($user->role == 'teacher')
                                    <span class="badge bg-warning">Teacher</span>
                                @else
                                    <span class="badge bg-success">Student</span>
                                @endif
                            </td>
                            <td>
                                @if($user->role == 'student')
                                    {{ $user->student_id ?? 'N/A' }}
                                @elseif($user->role == 'teacher')
                                    {{ $user->teacher_id ?? 'N/A' }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if($user->role == 'student')
                                    {{ $user->course }} - Year {{ $user->year_level }}
                                @else
                                    {{ $user->department ?? 'N/A' }}
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-success">Active</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-info">View</a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning">Edit</a>
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No users found. <a href="{{ route('admin.users.create') }}">Create one</a></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection