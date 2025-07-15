<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ObjectiveController;
use App\Http\Controllers\KeyResultController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserPermissionController;
use App\Http\Controllers\AnalyticsController;
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
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
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
    
    // Analytics (Super Admin Only)
    Route::prefix('analytics')->name('analytics.')->middleware('can:view-analytics')->group(function () {
        Route::get('/dashboard', [AnalyticsController::class, 'dashboard'])->name('dashboard');
        Route::get('/reports', [AnalyticsController::class, 'reports'])->name('reports');
        Route::get('/team-performance', [AnalyticsController::class, 'teamPerformance'])->name('team-performance');
        Route::get('/insights', [AnalyticsController::class, 'insights'])->name('insights');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
