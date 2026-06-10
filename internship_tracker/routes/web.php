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
use App\Http\Controllers\Teacher\SubjectQRCodeController;

// Custom login route
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Default auth routes
Route::post('/login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])
    ->name('logout')
    ->middleware('auth');
// Allow guests to visit the scan URL (they will be redirected to login but we save token)
Route::get('/student/attendance/scan/{token}', [App\Http\Controllers\Student\AttendanceController::class, 'scan'])
    ->name('student.attendance.scan');

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
    Route::post('/students/bulk-assign', [TeacherStudentController::class, 'bulkAssign'])->name('students.bulk-assign');
    
    // Attendance management
    Route::get('/attendance', [App\Http\Controllers\Teacher\AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/{attendance}', [App\Http\Controllers\Teacher\AttendanceController::class, 'show'])->name('attendance.show');
    Route::get('/attendance/student/{studentId}', [App\Http\Controllers\Teacher\AttendanceController::class, 'byStudent'])->name('attendance.by_student');
    
    // Manual attendance & quick time in/out
    Route::get('/students/{student}/attendance/create', [App\Http\Controllers\Teacher\AttendanceController::class, 'createManualAttendance'])->name('students.attendance.create');
    Route::post('/students/{student}/attendance/manual', [App\Http\Controllers\Teacher\AttendanceController::class, 'manualAttendance'])->name('students.attendance.manual');
    Route::post('/students/{student}/attendance/timein', [App\Http\Controllers\Teacher\AttendanceController::class, 'timeIn'])->name('students.attendance.timein');
    Route::post('/students/{student}/attendance/timeout', [App\Http\Controllers\Teacher\AttendanceController::class, 'timeOut'])->name('students.attendance.timeout');
    // Subject QR Codes
    Route::post('qrcode/generate', [SubjectQRCodeController::class, 'generate'])->name('qrcode.generate');
    Route::get('qrcode/{qrCode}', [SubjectQRCodeController::class, 'show'])->name('qrcode.show');
    Route::post('qrcode/{qrCode}/deactivate', [SubjectQRCodeController::class, 'deactivate'])->name('qrcode.deactivate');
    Route::get('qrcode/{qrCode}/scans', [SubjectQRCodeController::class, 'recentScans'])->name('qrcode.scans');
    // Delete student
    Route::delete('/students/{student}', [TeacherStudentController::class, 'destroy'])->name('students.destroy');
});
    
    // ============ STUDENT ROUTES ============
    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentAttendanceController::class, 'dashboard'])->name('dashboard');
        Route::post('/attendance/timein', [StudentAttendanceController::class, 'timeIn'])->name('attendance.timein');
        Route::post('/attendance/timeout', [StudentAttendanceController::class, 'timeOut'])->name('attendance.timeout');
        Route::get('/history', [StudentAttendanceController::class, 'attendanceHistory'])->name('history');
        
        Route::get('/scan', [StudentAttendanceController::class, 'showScanner'])->name('scan');
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