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
    
    // Analytics (Super Admin Only)
    Route::prefix('analytics')->middleware('can:view-analytics')->group(function () {
        Route::get('dashboard', [AnalyticsController::class, 'dashboard']);
        Route::get('success-rates', [AnalyticsController::class, 'successRates']);
        Route::get('team-performance', [AnalyticsController::class, 'teamPerformance']);
        Route::get('predictive-insights', [AnalyticsController::class, 'predictiveInsights']);
        Route::get('trends', [AnalyticsController::class, 'trends']);
        Route::post('custom-report', [AnalyticsController::class, 'customReport']);
        Route::get('performance-comparison', [AnalyticsController::class, 'performanceComparison']);
        Route::post('export', [AnalyticsController::class, 'export'])->middleware('can:export-analytics');
        Route::get('alerts', [AnalyticsController::class, 'alerts']);
    });
}); 