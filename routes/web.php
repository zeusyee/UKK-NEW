<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectMonitoringController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\LeaderController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\SubtaskController;
use App\Http\Controllers\MemberCardController;
use App\Http\Controllers\Admin\AdminReviewController;

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

        // Review routes
        Route::get('admin/review', [AdminReviewController::class, 'index'])
            ->name('admin.review.index');
        Route::get('admin/review/{card}', [AdminReviewController::class, 'show'])
            ->name('admin.review.show');
        Route::post('admin/review/{card}/approve', [AdminReviewController::class, 'approve'])
            ->name('admin.review.approve');
        Route::post('admin/review/{card}/reject', [AdminReviewController::class, 'reject'])
            ->name('admin.review.reject');
    });

    // Leader routes (for users with 'leader' or 'admin' role in a project)
    Route::prefix('leader')->name('leader.')->group(function () {
        Route::get('/dashboard', [LeaderController::class, 'dashboard'])->name('dashboard');
        Route::get('/projects/{project}', [LeaderController::class, 'projectDetails'])->name('project.details');
        
        // Board management
        Route::get('/projects/{project}/boards/create', [BoardController::class, 'create'])->name('board.create');
        Route::post('/projects/{project}/boards', [BoardController::class, 'store'])->name('board.store');
        Route::get('/projects/{project}/boards/{board}/edit', [BoardController::class, 'edit'])->name('board.edit');
        Route::put('/projects/{project}/boards/{board}', [BoardController::class, 'update'])->name('board.update');
        Route::delete('/projects/{project}/boards/{board}', [BoardController::class, 'destroy'])->name('board.destroy');
        
        // Card management
        Route::get('/projects/{project}/boards/{board}/cards/create', [CardController::class, 'create'])->name('card.create');
        Route::post('/projects/{project}/boards/{board}/cards', [CardController::class, 'store'])->name('card.store');
        Route::get('/projects/{project}/boards/{board}/cards/{card}', [CardController::class, 'show'])->name('card.show');
        Route::get('/projects/{project}/boards/{board}/cards/{card}/edit', [CardController::class, 'edit'])->name('card.edit');
        Route::put('/projects/{project}/boards/{board}/cards/{card}', [CardController::class, 'update'])->name('card.update');
        Route::delete('/projects/{project}/boards/{board}/cards/{card}', [CardController::class, 'destroy'])->name('card.destroy');
        
        // Subtask management
        Route::post('/projects/{project}/boards/{board}/cards/{card}/subtasks', [SubtaskController::class, 'store'])->name('subtask.store');
        Route::put('/projects/{project}/boards/{board}/cards/{card}/subtasks/{subtask}', [SubtaskController::class, 'update'])->name('subtask.update');
        Route::delete('/projects/{project}/boards/{board}/cards/{card}/subtasks/{subtask}', [SubtaskController::class, 'destroy'])->name('subtask.destroy');
    });

    // Member routes (regular members, not leaders)
    Route::middleware(['auth', \App\Http\Middleware\MemberMiddleware::class])->group(function () {
        Route::get('/dashboard', [MemberController::class, 'dashboard'])->name('member.dashboard');
        Route::get('/projects/{project}', [MemberController::class, 'projectDetails'])->name('member.project.details');
        
        // Member task routes
        Route::get('/my-tasks', [MemberCardController::class, 'myTasks'])->name('member.my-tasks');
        Route::get('/projects/{project}/boards/{board}/cards/{card}/task', [MemberCardController::class, 'showTask'])->name('member.task.show');
        Route::post('/projects/{project}/boards/{board}/cards/{card}/start', [MemberCardController::class, 'startTask'])->name('member.task.start');
        Route::post('/projects/{project}/boards/{board}/cards/{card}/submit', [MemberCardController::class, 'submitTask'])->name('member.task.submit');
    });
});