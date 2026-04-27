<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\WardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\SpatialController;
use App\Models\Report; // Added to fetch data for the dashboard map
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
     * Updated: Fetches all reports to populate the Spatial Awareness section on the dashboard.
     */
    Route::get('/admin/dashboard', function () {
        $reports = Report::all(); 
        return view('admin.dashboard', compact('reports')); 
    })->name('admin.dashboard');

    /**
     * Report Management (Task Tracking & Lifecycle)
     * Sticking to 'id' for uniformity as discussed.
     */
    Route::prefix('admin/reports')->name('admin.reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/{id}', [ReportController::class, 'show'])->name('show');
        Route::put('/{id}', [ReportController::class, 'update'])->name('update');
    });

    /**
     * Ward Management (Normalization & Localization)
     */
    Route::resource('/admin/wards', WardController::class)->names([
        'index'   => 'admin.wards.index',
        'store'   => 'admin.wards.store',
        'update'  => 'admin.wards.update',
        'destroy' => 'admin.wards.destroy',
    ]);

    /**
     * Department Management (Service Sectoring)
     */
    Route::resource('/admin/departments', DepartmentController::class)->names([
        'index'   => 'admin.departments.index',
        'store'   => 'admin.departments.store',
        'update'  => 'admin.departments.update',
        'destroy' => 'admin.departments.destroy',
    ]);

    /**
     * Spatial Awareness (Live Surveillance & Mapping)
     */
    Route::get('/admin/spatial', [SpatialController::class, 'index'])->name('admin.spatial.index');

    /**
     * Audit Trail (Accountability & Logging)
     */
    Route::get('/admin/logs', [AuditLogController::class, 'index'])->name('admin.logs.index');

    /**
     * Authentication
     */
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});