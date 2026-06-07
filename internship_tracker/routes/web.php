<?php

use App\Http\Controllers\Admin\DashboardController;  // Changed from AdminDashboardController
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\StudentController as TeacherStudentController;
use App\Http\Controllers\Student\AttendanceController as StudentAttendanceController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

// Custom login route
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Registration routes
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('guest');

// Default auth routes
Route::post('/login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])
    ->name('logout')
    ->middleware('auth');

// Protected routes (require authentication)
Route::middleware(['auth'])->group(function () {
    
    // ============ ADMIN ROUTES ============
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        // Dashboard - Using DashboardController instead of AdminDashboardController
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // User management
        Route::resource('users', UserController::class);
        
        // Subject management
        Route::resource('subjects', SubjectController::class);
        
        // Section management
        Route::resource('sections', SectionController::class);
        
        // Enrollment management
        Route::resource('enrollments', EnrollmentController::class);
        
        // Attendance management
        Route::get('/attendances', [AdminAttendanceController::class, 'index'])->name('attendances.index');
        Route::get('/attendances/{attendance}', [AdminAttendanceController::class, 'show'])->name('attendances.show');
        Route::get('/students/{student}/attendance', [AdminAttendanceController::class, 'studentAttendance'])->name('students.attendance');
        Route::get('/subjects/{subject}/attendance', [AdminAttendanceController::class, 'subjectAttendance'])->name('subjects.attendance');
        Route::post('/reports/attendance', [AdminAttendanceController::class, 'generateReport'])->name('reports.attendance');
    });
    
    // ============ TEACHER ROUTES ============
    Route::middleware(['role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
        Route::get('/students', [TeacherStudentController::class, 'index'])->name('students.index');
        Route::get('/students/{student}', [TeacherStudentController::class, 'show'])->name('students.show');
    });
    
    // ============ STUDENT ROUTES ============
    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentAttendanceController::class, 'dashboard'])->name('dashboard');
        Route::post('/attendance/timein', [StudentAttendanceController::class, 'timeIn'])->name('attendance.timein');
        Route::post('/attendance/timeout', [StudentAttendanceController::class, 'timeOut'])->name('attendance.timeout');
        Route::get('/history', [StudentAttendanceController::class, 'attendanceHistory'])->name('history');
    });
    
    // Simple dashboard routes for backward compatibility
    Route::get('/teacher/dashboard', function () {
        return view('teacher.dashboard');
    })->middleware(['role:teacher'])->name('teacher.dashboard');
    
    Route::get('/student/dashboard', function () {
        return view('student.dashboard');
    })->middleware(['role:student'])->name('student.dashboard');
    
    // Single dashboard route that redirects based on role
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isTeacher()) {
            return redirect()->route('teacher.dashboard');
        } else {
            return redirect()->route('student.dashboard');
        }
    })->name('dashboard');
});