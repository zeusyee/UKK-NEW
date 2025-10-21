<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectMonitoringController;

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Admin routes
    Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
        Route::get('/admin/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');

        // Project routes
        Route::resource('admin/projects', ProjectController::class)->names([
            'index' => 'admin.projects.index',
            'create' => 'admin.projects.create',
            'store' => 'admin.projects.store',
            'show' => 'admin.projects.show',
            'edit' => 'admin.projects.edit',
            'update' => 'admin.projects.update',
            'destroy' => 'admin.projects.destroy',
        ]);
        
        // Project member management routes
        Route::get('admin/projects/{project}/members', [ProjectController::class, 'members'])
            ->name('admin.projects.members');
        Route::post('admin/projects/{project}/members', [ProjectController::class, 'addMember'])
            ->name('admin.projects.add-member');
        Route::delete('admin/projects/{project}/members/{member}', [ProjectController::class, 'removeMember'])
            ->name('admin.projects.remove-member');

        // User management routes
        Route::resource('admin/users', UserController::class)->names([
            'index' => 'admin.users.index',
            'create' => 'admin.users.create',
            'store' => 'admin.users.store',
            'show' => 'admin.users.show',
            'edit' => 'admin.users.edit',
            'update' => 'admin.users.update',
            'destroy' => 'admin.users.destroy',
        ]);

        // Project Monitoring routes
        Route::get('admin/monitoring', [ProjectMonitoringController::class, 'index'])
            ->name('admin.monitoring.index');
        Route::get('admin/monitoring/projects/{project}', [ProjectMonitoringController::class, 'projectDetails'])
            ->name('admin.monitoring.project-details');
    });

    // User routes
    Route::middleware(['auth', \App\Http\Middleware\MemberMiddleware::class])->group(function () {
        Route::get('/dashboard', function () {
            return view('user.dashboard');
        })->name('user.dashboard');
    });
});
