<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\InternshipController;  // Added - changed from EnrollmentController
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\StudentController as TeacherStudentController;
use App\Http\Controllers\Student\AttendanceController as StudentAttendanceController;
use Illuminate\Support\Facades\Route;

// Custom login route
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Default auth routes
Route::post('/login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])
    ->name('logout')
    ->middleware('auth');

// Protected routes (require authentication)
Route::middleware(['auth'])->group(function () {
    
    // ============ ADMIN ROUTES ============
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('users', UserController::class);
        Route::resource('subjects', SubjectController::class);
        Route::resource('sections', SectionController::class);
        Route::resource('internships', InternshipController::class);  // Now works with the use statement
        Route::get('/attendances', [AdminAttendanceController::class, 'index'])->name('attendances.index');
        Route::get('/attendances/{attendance}', [AdminAttendanceController::class, 'show'])->name('attendances.show');
        Route::get('/students/{student}/attendance', [AdminAttendanceController::class, 'studentAttendance'])->name('students.attendance');
        Route::get('/subjects/{subject}/attendance', [AdminAttendanceController::class, 'subjectAttendance'])->name('subjects.attendance');
        Route::post('/reports/attendance', [AdminAttendanceController::class, 'generateReport'])->name('reports.attendance');
    });
    
    // ============ TEACHER ROUTES ============
Route::middleware(['role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
    
    // Student management
    Route::resource('students', TeacherStudentController::class);
    
    // Internship management
    Route::resource('internships', App\Http\Controllers\Teacher\InternshipController::class);
    Route::post('/students/bulk-assign', [App\Http\Controllers\Teacher\StudentController::class, 'bulkAssign'])->name('students.bulk-assign');
    // Attendance management - ADD THE MISSING ROUTES
    Route::get('/attendance', [App\Http\Controllers\Teacher\AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/{attendance}', [App\Http\Controllers\Teacher\AttendanceController::class, 'show'])->name('attendance.show');  // ADD THIS LINE
    Route::get('/attendance/student/{studentId}', [App\Http\Controllers\Teacher\AttendanceController::class, 'byStudent'])->name('attendance.by_student');
});
    
    // ============ STUDENT ROUTES ============
    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentAttendanceController::class, 'dashboard'])->name('dashboard');
        Route::post('/attendance/timein', [StudentAttendanceController::class, 'timeIn'])->name('attendance.timein');
        Route::post('/attendance/timeout', [StudentAttendanceController::class, 'timeOut'])->name('attendance.timeout');
        Route::get('/history', [StudentAttendanceController::class, 'attendanceHistory'])->name('history');
    });
    
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