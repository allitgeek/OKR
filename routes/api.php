<?php

use App\Http\Controllers\Api\ObjectiveController;
use App\Http\Controllers\Api\KeyResultController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\AnalyticsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Objectives
    Route::apiResource('objectives', ObjectiveController::class);
    
    // Key Results
    Route::get('objectives/{objective}/key-results', [KeyResultController::class, 'index']);
    Route::post('objectives/{objective}/key-results', [KeyResultController::class, 'store']);
    Route::get('key-results/{keyResult}', [KeyResultController::class, 'show']);
    Route::put('key-results/{keyResult}', [KeyResultController::class, 'update']);
    Route::delete('key-results/{keyResult}', [KeyResultController::class, 'destroy']);
    Route::patch('key-results/{keyResult}/progress', [KeyResultController::class, 'updateProgress']);
    
    // Tasks
    Route::apiResource('tasks', TaskController::class);
    Route::post('tasks/{task}/accept', [TaskController::class, 'accept']);
    Route::post('tasks/{task}/reject', [TaskController::class, 'reject']);
    Route::post('tasks/{task}/complete', [TaskController::class, 'complete']);
});

 