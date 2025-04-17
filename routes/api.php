<?php

use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Middleware\EnsureBuildingAccess;
use App\Http\Middleware\EnsureTaskAccess;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // Task routes
    Route::get('/buildings/{building}/tasks', [TaskController::class, 'index'])
        ->middleware(EnsureBuildingAccess::class)
        ->name('buildings.tasks.index');
    
    Route::post('/tasks', [TaskController::class, 'store'])
        ->name('tasks.store');
    
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])
        ->middleware(EnsureTaskAccess::class)
        ->name('tasks.status.update');
    
    // Comment routes
    Route::post('/tasks/{task}/comments', [CommentController::class, 'store'])
        ->name('tasks.comments.store');
});