<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\ProjectApiController;
use App\Http\Controllers\Api\CardApiController;
use App\Http\Controllers\Api\SubtaskApiController;
use App\Http\Controllers\Api\MemberApiController;

/*
|--------------------------------------------------------------------------
| API Routes for Flutter Mobile App (Member Role)
|--------------------------------------------------------------------------
|
| Routes untuk aplikasi Flutter dengan role member
| Base URL: /api/v1
|
*/

Route::prefix('v1')->group(function () {
    // Authentication Routes (No Auth Required)
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthApiController::class, 'login']);
        Route::post('/register', [AuthApiController::class, 'register']);
        Route::post('/google-login', [AuthApiController::class, 'googleLogin']);
    });

    // Protected Routes (Requires API Token)
    Route::middleware(['auth:api'])->group(function () {
        // Auth Routes
        Route::prefix('auth')->group(function () {
            Route::post('/logout', [AuthApiController::class, 'logout']);
            Route::get('/me', [AuthApiController::class, 'getProfile']);
            Route::put('/profile', [AuthApiController::class, 'updateProfile']);
        });

        // Member Dashboard
        Route::prefix('member')->group(function () {
            Route::get('/dashboard', [MemberApiController::class, 'dashboard']);
            Route::get('/statistics', [MemberApiController::class, 'statistics']);
        });

        // Projects - Member can only view their projects
        Route::prefix('projects')->group(function () {
            Route::get('/', [ProjectApiController::class, 'index']);
            Route::get('/{project}', [ProjectApiController::class, 'show']);
            Route::get('/{project}/boards', [ProjectApiController::class, 'boards']);
        });

        // Boards
        Route::prefix('boards')->group(function () {
            Route::get('/{board}/cards', [CardApiController::class, 'cardsByBoard']);
        });

        // Cards - Member can view and interact with assigned cards
        Route::prefix('cards')->group(function () {
            Route::get('/', [CardApiController::class, 'index']);
            Route::get('/my-tasks', [CardApiController::class, 'myTasks']);
            Route::get('/{card}', [CardApiController::class, 'show']);
            Route::post('/{card}/start', [CardApiController::class, 'startCard']);
            Route::get('/{card}/assignment', [CardApiController::class, 'getAssignment']);
        });

        // Subtasks - Member can manage their subtasks
        Route::prefix('subtasks')->group(function () {
            Route::post('/{card}/create', [SubtaskApiController::class, 'store']);
            Route::get('/{subtask}', [SubtaskApiController::class, 'show']);
            Route::put('/{subtask}', [SubtaskApiController::class, 'update']);
            Route::delete('/{subtask}', [SubtaskApiController::class, 'destroy']);
            Route::post('/{subtask}/start', [SubtaskApiController::class, 'start']);
            Route::post('/{subtask}/pause', [SubtaskApiController::class, 'pause']);
            Route::post('/{subtask}/resume', [SubtaskApiController::class, 'resume']);
            Route::post('/{subtask}/submit', [SubtaskApiController::class, 'submit']);
        });

        // Time Logs
        Route::prefix('time-logs')->group(function () {
            Route::get('/', [SubtaskApiController::class, 'timeLogs']);
            Route::get('/subtask/{subtask}', [SubtaskApiController::class, 'subtaskTimeLogs']);
        });
    });
});
