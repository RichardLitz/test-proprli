<?php

use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\CommentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Task routes
    Route::get('/buildings/{building}/tasks', [TaskController::class, 'index']);
    Route::post('/tasks', [TaskController::class, 'store']);
    
    // Comment routes
    Route::post('/tasks/{task}/comments', [CommentController::class, 'store']);
});
