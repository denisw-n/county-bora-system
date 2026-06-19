<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController; 
use App\Http\Controllers\Auth\ResetPasswordController;   
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\WardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\SpatialController;
use App\Http\Controllers\Admin\PublicCommController; 
use App\Http\Controllers\Admin\UserController; 
use App\Http\Controllers\Admin\HotlineController; 
use App\Http\Controllers\Admin\TransparencyController;
use App\Http\Controllers\Admin\InvitationController;
use App\Http\Controllers\Admin\ProfileController; // Added
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

// --- Password Reset Routes ---
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->middleware('guest')->name('password.update');

// --- Public Invitation Routes ---
Route::get('/admin/invitations/accept/{token}', [InvitationController::class, 'accept'])->name('admin.invitations.accept');
Route::post('/register/submit', [AuthController::class, 'adminRegister'])->name('register.submit');

// --- Protected Admin Routes ---
Route::middleware(['auth'])->group(function () {
    
    /**
     * Main Dashboard
     */
    Route::get('/admin/dashboard', function () {
        $reports = Report::all(); 
        $totalReports = $reports->count();
        
        $resolvedCount = $reports->where('status', 'resolved')->count();
        $resolutionRate = $totalReports > 0 ? round(($resolvedCount / $totalReports) * 100, 1) : 0;
        
        $systemHealth = 99.9;

        return view('admin.dashboard', compact('reports', 'totalReports', 'resolutionRate', 'systemHealth')); 
    })->name('admin.dashboard');

    /**
     * Admin Profile Module
     */
    Route::get('/admin/profile', [ProfileController::class, 'show'])->name('admin.profile.show');

    /**
     * Admin Invitation Module
     */
    Route::prefix('admin/invitations')->name('admin.invitations.')->group(function () {
        Route::get('/create', [InvitationController::class, 'create'])->name('create');
        Route::post('/store', [InvitationController::class, 'store'])->name('store');
    });

    /**
     * Transparency Module Routes
     */
    Route::prefix('admin/transparency')->name('admin.transparency.')->group(function () {
        Route::get('/', [TransparencyController::class, 'index'])->name('index');
        Route::post('/refresh', [TransparencyController::class, 'refreshStats'])->name('refresh');
    });

    /**
     * Report Management
     */
    Route::prefix('admin/reports')->name('admin.reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        
        Route::get('/ratings', [ReportController::class, 'viewRatings'])->name('ratings.view');
        Route::get('/ratings/data', [ReportController::class, 'getRatings'])->name('ratings.data');
        
        Route::get('/search-prediction', [ReportController::class, 'search'])->name('search');
        Route::post('/quick-status-update', [ReportController::class, 'quickStatusUpdate'])->name('quickStatusUpdate');
        
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
        Route::get('/verification', [UserController::class, 'verificationIndex'])->name('verification');
        Route::patch('/{user}/verify', [UserController::class, 'toggleVerification'])->name('verify');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::get('/', [UserController::class, 'index'])->name('index');
    });

    /**
     * Ward Management
     */
    Route::resource('/admin/wards', WardController::class)->names([
        'index'   => 'admin.wards.index',
        'store'   => 'admin.wards.store',
        'show'    => 'admin.wards.show',
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