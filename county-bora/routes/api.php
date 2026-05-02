<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\NotificationController; // Added this import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for County Bora App
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (Requires Bearer Token)
Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);

    // Citizen Profile
    Route::get('/profile', function (Request $request) {
        return $request->user();
    });

    // --- Admin & Public Utility Routes ---
    Route::get('/admin/pending-users', [AdminController::class, 'getPendingUsers']);
    Route::post('/admin/verify-user/{id}', [AdminController::class, 'verifyUser']);

    // --- Verified Citizen Only Routes ---
    Route::middleware('is_verified')->group(function () {
        // Incident Reporting
        Route::post('/reports', [ReportController::class, 'store']);

        // --- NEW: Personal Notifications ---
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount']);
        Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    });
    
    // History
    Route::get('/my-reports', [ReportController::class, 'myReports']);
    
});