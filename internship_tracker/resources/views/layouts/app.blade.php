<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Internship Tracker')</title>
    
    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .navbar-custom {
            background-color: #216699;
        }
        .navbar-custom .navbar-brand,
        .navbar-custom .nav-link {
            color: white;
        }
        .navbar-custom .nav-link:hover {
            color: #ede432;
        }
        .logout-btn {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            padding: 0.5rem 1rem;
            display: block;
            width: 100%;
            text-align: left;
        }
        .logout-btn:hover {
            color: #ede432;
        }
        
        /* Sidebar Styles */
        .sidebar {
            min-height: 100vh;
            background-color: #216699;
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            z-index: 100;
        }
        .sidebar .nav-link {
            color: white;
            padding: 12px 20px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            background-color: #174b73;
        }
        .sidebar .nav-link.active {
            background-color: #ede432;
            color: #216699;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .navbar-custom {
                margin-left: 0 !important;
            }
        }
        
        /* Card Stats */
        .card-stats {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .card-stats:hover {
            transform: translateY(-5px);
        }
        .progress {
            height: 8px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="text-center py-4">
            <h4 class="text-white">Internship Tracker</h4>
            <small class="text-white-50">{{ ucfirst(auth()->user()->role ?? 'Guest') }} Portal</small>
        </div>
        <hr class="text-white-50 mx-3">
        <nav class="nav flex-column">
            @auth
                @if(auth()->user()->isAdmin())
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                       href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}" 
                       href="{{ route('admin.subjects.index') }}">
                        <i class="fas fa-book"></i> Subjects
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.sections.*') ? 'active' : '' }}" 
                       href="{{ route('admin.sections.index') }}">
                        <i class="fas fa-users"></i> Sections
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.internships.*') ? 'active' : '' }}" 
                       href="{{ route('admin.internships.index') }}">
                        <i class="fas fa-briefcase"></i> Internships
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.attendances.*') ? 'active' : '' }}" 
                       href="{{ route('admin.attendances.index') }}">
                        <i class="fas fa-clock"></i> Attendances
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" 
                       href="{{ route('admin.users.index') }}">
                        <i class="fas fa-user-cog"></i> Users
                    </a>
               @elseif(auth()->user()->isTeacher())
                    <a class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}" 
                    href="{{ route('teacher.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a class="nav-link {{ request()->routeIs('teacher.students.*') ? 'active' : '' }}" 
                    href="{{ route('teacher.students.index') }}">
                        <i class="fas fa-users"></i> Section Internship
                    </a>
                    <a class="nav-link {{ request()->routeIs('teacher.attendance.*') ? 'active' : '' }}" 
                    href="{{ route('teacher.attendance.index') }}">
                        <i class="fas fa-clock"></i> Attendance
                    </a>
                
                @elseif(auth()->user()->isStudent())
                    <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" 
                       href="{{ route('student.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a class="nav-link {{ request()->routeIs('student.history') ? 'active' : '' }}" 
                       href="{{ route('student.history') }}">
                        <i class="fas fa-history"></i> Attendance History
                    </a>
                   
                @endif
            @endauth
        </nav>
        <hr class="text-white-50 mx-3">
        <div class="p-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-light w-100">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Top Navbar -->
    <nav class="navbar navbar-custom navbar-expand-lg" style="margin-left: 250px;">
        <div class="container-fluid">
            <button class="btn btn-link text-white d-md-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <a class="navbar-brand d-md-none" href="{{ url('/') }}">
                Internship Tracker
            </a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> {{ Auth::user()->name ?? 'User' }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-item-text">
                                <small>Role: {{ ucfirst(Auth::user()->role ?? 'Guest') }}</small>
                            </span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Scripts -->
    <!-- jQuery (required for some admin scripts) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });
    </script>
    @stack('scripts')
</body>
</html>