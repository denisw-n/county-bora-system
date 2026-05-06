<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\AlertController; 
use App\Http\Controllers\Api\HotlineController;
use App\Http\Controllers\Api\SpatialController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for County Bora App
|--------------------------------------------------------------------------
*/

// --- PUBLIC ROUTES ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/**
 * FETCH WARDS
 * Moved here to allow the Flutter RegisterScreen to populate 
 * the Ward dropdown before a user is authenticated.
 */
Route::get('/wards', [SpatialController::class, 'getWards']); 

/**
 * DEPARTMENTS AS CATEGORIES (NOW PUBLIC)
 * Allows ReportIssueScreen to fetch categories without authentication.
 */
Route::get('/departments', [ReportController::class, 'getDepartments']); 

// --- PROTECTED ROUTES (Requires Bearer Token) ---
Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);

    // --- Citizen Profile ---
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::patch('/profile/update', [ProfileController::class, 'update']);

    // --- Admin & Public Utility Routes ---
    Route::get('/admin/pending-users', [AdminController::class, 'getPendingUsers']);
    Route::post('/admin/verify-user/{id}', [AdminController::class, 'verifyUser']);

    // --- Public Alerts ---
    Route::get('/alerts', [AlertController::class, 'index']);
    Route::get('/alerts/{id}', [AlertController::class, 'show']);

    // --- Hotlines ---
    Route::get('/hotlines', [HotlineController::class, 'index']);

    // --- Spatial Data ---
    Route::get('/my-map-markers', [SpatialController::class, 'getMyMapMarkers']);

    // --- Verified Citizen Only Routes ---
    Route::middleware('is_verified')->group(function () {
        // Incident Reporting
        Route::post('/reports', [ReportController::class, 'store']);

        // --- NEW: Report Rating ---
        Route::post('/reports/{id}/rate', [ReportController::class, 'rateReport']);

        // --- Personal Notifications ---
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
        Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    });
    
    // History
    Route::get('/my-reports', [ReportController::class, 'myReports']);
    
});

