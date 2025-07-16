<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ObjectiveController;
use App\Http\Controllers\KeyResultController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserPermissionController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\OkrCycleController;
use App\Http\Controllers\OkrCheckInController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
|
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Debug route (temporary)
    Route::get('/debug-user', function () {
        return view('debug-user');
    })->name('debug.user');
    
    // User Management
    Route::post('/users/create', [UserPermissionController::class, 'createUser'])->name('users.create');
    Route::put('/users/{user}', [UserPermissionController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [UserPermissionController::class, 'deleteUser'])->name('users.delete');
    Route::patch('/users/{user}/toggle-status', [UserPermissionController::class, 'toggleUserStatus'])->name('users.toggle-status');
    
    // Objectives
    Route::resource('objectives', ObjectiveController::class);
    Route::post('objectives/{objective}/key-results', [ObjectiveController::class, 'addKeyResult'])->name('objectives.key-results.store');
    Route::post('/objectives/create', [DashboardController::class, 'createObjective'])->name('objectives.create.super');
    
    // Key Results
    Route::resource('key-results', KeyResultController::class);
    Route::patch('key-results/{keyResult}/progress', [KeyResultController::class, 'updateProgress'])->name('key-results.update-progress');
    Route::patch('key-results/{keyResult}/complete', [KeyResultController::class, 'markComplete'])->name('key-results.complete');
    
    // Tasks
    Route::resource('tasks', TaskController::class);
    Route::post('tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
    Route::post('/tasks/create', [DashboardController::class, 'createTask'])->name('tasks.create.super');

    // User Permissions Management
    Route::get('/users/permissions', [UserPermissionController::class, 'index'])->name('users.permissions.index');
    Route::put('/users/{user}/permissions', [UserPermissionController::class, 'update'])->name('users.permissions.update');
    Route::post('/users/{user}/super-admin', [UserPermissionController::class, 'assignSuperAdmin'])->name('users.permissions.super-admin');
    
    // OKR Cycles Management
    Route::resource('okr-cycles', OkrCycleController::class);
    Route::post('okr-cycles/{cycle}/start', [OkrCycleController::class, 'startCycle'])->name('okr-cycles.start');
    Route::post('okr-cycles/{cycle}/close', [OkrCycleController::class, 'closeCycle'])->name('okr-cycles.close');
    Route::post('okr-cycles/initialize-year', [OkrCycleController::class, 'initializeYear'])->name('okr-cycles.initialize-year');
    Route::get('okr-dashboard', [OkrCycleController::class, 'dashboard'])->name('okr.dashboard');

    // OKR Check-ins
    Route::resource('okr-check-ins', OkrCheckInController::class);
    Route::post('okr-check-ins/quick', [OkrCheckInController::class, 'quickCheckIn'])->name('okr-check-ins.quick');

    // Analytics (Super Admin Only)
    Route::prefix('analytics')->name('analytics.')->middleware('can:view-analytics')->group(function () {
        Route::get('/dashboard', [AnalyticsController::class, 'dashboard'])->name('dashboard');
        Route::get('/reports', [AnalyticsController::class, 'reports'])->name('reports');
        Route::get('/team-performance', [AnalyticsController::class, 'teamPerformance'])->name('team-performance');
        Route::get('/insights', [AnalyticsController::class, 'insights'])->name('insights');
    });
    
    // Analytics API endpoints (for AJAX calls)
    Route::prefix('api/analytics')->middleware('can:view-analytics')->group(function () {
        Route::get('dashboard', [App\Http\Controllers\Api\AnalyticsController::class, 'dashboard']);
        Route::get('success-rates', [App\Http\Controllers\Api\AnalyticsController::class, 'successRates']);
        Route::get('team-performance', [App\Http\Controllers\Api\AnalyticsController::class, 'teamPerformance']);
        Route::get('predictive-insights', [App\Http\Controllers\Api\AnalyticsController::class, 'predictiveInsights']);
        Route::get('trends', [App\Http\Controllers\Api\AnalyticsController::class, 'trends']);
        Route::post('custom-report', [App\Http\Controllers\Api\AnalyticsController::class, 'customReport']);
        Route::get('performance-comparison', [App\Http\Controllers\Api\AnalyticsController::class, 'performanceComparison']);
        Route::post('export', [App\Http\Controllers\Api\AnalyticsController::class, 'export'])->middleware('can:export-analytics');
        Route::get('alerts', [App\Http\Controllers\Api\AnalyticsController::class, 'alerts']);
    });
});

// OKR Management Routes
Route::middleware('auth')->group(function () {
    // OKR Dashboard
    Route::get('okr/dashboard', [App\Http\Controllers\OkrCycleController::class, 'dashboard'])->name('okr.dashboard');
    
    // OKR Cycles
    Route::resource('okr-cycles', App\Http\Controllers\OkrCycleController::class);
    Route::post('/okr-cycles/{cycle}/start', [App\Http\Controllers\OkrCycleController::class, 'start'])->name('okr-cycles.start');
    Route::post('/okr-cycles/{cycle}/close', [App\Http\Controllers\OkrCycleController::class, 'close'])->name('okr-cycles.close');
    Route::post('/okr-cycles/init/{year}', [App\Http\Controllers\OkrCycleController::class, 'initializeYear'])->name('okr-cycles.init');
    
    // OKR Check-ins
    Route::resource('okr-check-ins', App\Http\Controllers\OkrCheckInController::class);
    Route::post('/okr-check-ins/quick', [App\Http\Controllers\OkrCheckInController::class, 'quickCheckIn'])->name('okr-check-ins.quick');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
