<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProfileController;
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
    
    // Dashboard routes for different roles
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
        ->middleware(['role:admin'])
        ->name('admin.dashboard');
    
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
    
    // Admin only routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
    });

});