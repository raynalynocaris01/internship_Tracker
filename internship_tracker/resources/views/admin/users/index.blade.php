@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2"
             style="background-color: #216699; color: white;">
            <h3 class="mb-0">User Management</h3>
            <a href="{{ route('admin.users.create') }}" class="btn btn-light btn-sm">
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

            {{-- Role Filter Tabs (scrollable on mobile) --}}
            <ul class="nav nav-tabs mb-3 flex-nowrap overflow-auto" style="white-space: nowrap;">
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

            @if($users->count() > 0)
                {{-- Mobile‑friendly user cards --}}
                @foreach($users as $user)
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body p-3">
                            {{-- Header: Name + Role + Actions --}}
                            <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-2">
                                <div>
                                    <strong class="fs-6">{{ $user->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </div>
                                <div class="d-flex gap-2">
                                    @if($user->role == 'admin')
                                        <span class="badge bg-danger">Admin</span>
                                    @elseif($user->role == 'teacher')
                                        <span class="badge bg-warning text-dark">Teacher</span>
                                    @else
                                        <span class="badge bg-success">Student</span>
                                    @endif
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this user?')" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Details row (2 columns on mobile, 4 on larger) --}}
                            <div class="row g-2 small mt-2">
                                <div class="col-6 col-md-3">
                                    <span class="text-muted">ID</span><br>
                                    <strong>{{ $user->id }}</strong>
                                </div>
                                <div class="col-6 col-md-3">
                                    <span class="text-muted">ID Number</span><br>
                                    @if($user->role == 'student')
                                        {{ $user->student_id ?? 'N/A' }}
                                    @elseif($user->role == 'teacher')
                                        {{ $user->teacher_id ?? 'N/A' }}
                                    @else
                                        —
                                    @endif
                                </div>
                                <div class="col-6 col-md-3">
                                    <span class="text-muted">Course / Dept</span><br>
                                    @if($user->role == 'student')
                                        {{ $user->course }} - Year {{ $user->year_level }}
                                    @else
                                        {{ $user->department ?? 'N/A' }}
                                    @endif
                                </div>
                                <div class="col-6 col-md-3">
                                    <span class="text-muted">Status</span><br>
                                    <span class="badge bg-success">Active</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Pagination --}}
                <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="small text-muted">
                        Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} 
                        of {{ $users->total() ?? 0 }} users
                    </div>
                    <div>
                        {{ $users->links() }}
                    </div>
                </div>

            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No users found.</p>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create your first user
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection