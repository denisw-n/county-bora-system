<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\WardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\SpatialController;
use App\Http\Controllers\Admin\PublicCommController; 
use App\Http\Controllers\Admin\UserController; 
use App\Http\Controllers\Admin\HotlineController; 
use App\Models\Report; 
use Illuminate\Support\Facades\Route;

// --- Public Routes ---
Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', function () {
    return view('auth.login'); 
})->name('login');

Route::post('/login', [AuthController::class, 'login']);

// --- Protected Admin Routes ---
Route::middleware(['auth'])->group(function () {
    
    /**
     * Main Dashboard
     */
    Route::get('/admin/dashboard', function () {
        $reports = Report::all(); 
        return view('admin.dashboard', compact('reports')); 
    })->name('admin.dashboard');

    /**
     * Report Management
     */
    Route::prefix('admin/reports')->name('admin.reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/{id}', [ReportController::class, 'show'])->name('show');
        Route::put('/{id}', [ReportController::class, 'update'])->name('update');
    });

    /**
     * Public Communication
     */
    Route::prefix('admin/communication')->name('admin.communication.')->group(function () {
        Route::get('/', [PublicCommController::class, 'index'])->name('index');
        Route::post('/broadcast', [PublicCommController::class, 'broadcast'])->name('broadcast');
        Route::get('/search-users', [PublicCommController::class, 'searchUsers'])->name('users.search');
    });

    /**
     * User Management & Verification
     */
    Route::prefix('admin/users')->name('admin.users.')->group(function () {
        // Queue for unverified citizens
        Route::get('/verification', [UserController::class, 'verificationIndex'])->name('verification');
        
        // Approve/Toggle Verification
        Route::patch('/{user}/verify', [UserController::class, 'toggleVerification'])->name('verify');
        
        // Reject/Delete User (This was missing)
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        
        // List all citizens
        Route::get('/', [UserController::class, 'index'])->name('index');
    });

    /**
     * Ward Management
     */
    Route::resource('/admin/wards', WardController::class)->names([
        'index'   => 'admin.wards.index',
        'store'   => 'admin.wards.store',
        'update'  => 'admin.wards.update',
        'destroy' => 'admin.wards.destroy',
    ]);

    /**
     * Department Management
     */
    Route::resource('/admin/departments', DepartmentController::class)->names([
        'index'   => 'admin.departments.index',
        'store'   => 'admin.departments.store',
        'update'  => 'admin.departments.update',
        'destroy' => 'admin.departments.destroy',
    ]);

    /**
     * Emergency Hotlines
     */
    Route::resource('/admin/hotlines', HotlineController::class)->names([
        'index'   => 'admin.hotlines.index',
        'store'   => 'admin.hotlines.store',
        'update'  => 'admin.hotlines.update',
        'destroy' => 'admin.hotlines.destroy',
    ])->except(['show', 'create', 'edit']);

    /**
     * Spatial Awareness
     */
    Route::get('/admin/spatial', [SpatialController::class, 'index'])->name('admin.spatial.index');

    /**
     * Audit Trail
     */
    Route::get('/admin/logs', [AuditLogController::class, 'index'])->name('admin.logs.index');

    /**
     * Authentication
     */
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});