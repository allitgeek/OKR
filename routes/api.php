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
    // Objectives API
    Route::apiResource('objectives', ObjectiveController::class)->names([
        'index' => 'api.objectives.index',
        'store' => 'api.objectives.store',
        'show' => 'api.objectives.show',
        'update' => 'api.objectives.update',
        'destroy' => 'api.objectives.destroy',
    ]);
    
    // Key Results API
    Route::get('objectives/{objective}/key-results', [KeyResultController::class, 'index'])->name('api.key-results.index');
    Route::post('objectives/{objective}/key-results', [KeyResultController::class, 'store'])->name('api.key-results.store');
    Route::get('key-results/{keyResult}', [KeyResultController::class, 'show'])->name('api.key-results.show');
    Route::put('key-results/{keyResult}', [KeyResultController::class, 'update'])->name('api.key-results.update');
    Route::delete('key-results/{keyResult}', [KeyResultController::class, 'destroy'])->name('api.key-results.destroy');
    Route::patch('key-results/{keyResult}/progress', [KeyResultController::class, 'updateProgress'])->name('api.key-results.update-progress');
    
    // Tasks API
    Route::apiResource('tasks', TaskController::class)->names([
        'index' => 'api.tasks.index',
        'store' => 'api.tasks.store',
        'show' => 'api.tasks.show',
        'update' => 'api.tasks.update',
        'destroy' => 'api.tasks.destroy',
    ]);
    Route::post('tasks/{task}/accept', [TaskController::class, 'accept'])->name('api.tasks.accept');
    
    // OKR Cycles API
    Route::apiResource('okr-cycles', \App\Http\Controllers\Api\OkrCycleController::class)->names([
        'index' => 'api.okr-cycles.index',
        'store' => 'api.okr-cycles.store',
        'show' => 'api.okr-cycles.show',
        'update' => 'api.okr-cycles.update',
        'destroy' => 'api.okr-cycles.destroy',
    ]);
    Route::post('okr-cycles/{cycle}/start', [\App\Http\Controllers\Api\OkrCycleController::class, 'startCycle'])->name('api.okr-cycles.start');
    Route::get('okr-cycles/health/report', [\App\Http\Controllers\Api\OkrCycleController::class, 'healthReport'])->name('api.okr-cycles.health');
    
    // OKR Check-ins API
    Route::apiResource('okr-check-ins', \App\Http\Controllers\Api\OkrCheckInController::class)->names([
        'index' => 'api.okr-check-ins.index',
        'store' => 'api.okr-check-ins.store',
        'show' => 'api.okr-check-ins.show',
        'update' => 'api.okr-check-ins.update',
        'destroy' => 'api.okr-check-ins.destroy',
    ]);
    Route::post('okr-check-ins/quick', [\App\Http\Controllers\Api\OkrCheckInController::class, 'quickCheckIn'])->name('api.okr-check-ins.quick');
    Route::get('okr-check-ins/analytics', [\App\Http\Controllers\Api\OkrCheckInController::class, 'analytics'])->name('api.okr-check-ins.analytics');
    Route::post('tasks/{task}/reject', [TaskController::class, 'reject'])->name('api.tasks.reject');
    Route::post('tasks/{task}/complete', [TaskController::class, 'complete'])->name('api.tasks.complete');
});

 