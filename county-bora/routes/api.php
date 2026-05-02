<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\AdminController;
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

    // --- Admin Verification Routes ---
    // Note: In production, you should wrap these in a 'role:admin' middleware
    Route::get('/admin/pending-users', [AdminController::class, 'getPendingUsers']);
    Route::post('/admin/verify-user/{id}', [AdminController::class, 'verifyUser']);

    // --- Verified Citizen Only Routes ---
    // This uses the alias created in bootstrap/app.php
    Route::middleware('is_verified')->group(function () {
        // Only verified users can submit new incident reports
        Route::post('/reports', [ReportController::class, 'store']);
    });
    
    // Any logged-in user can still see their own report history
    Route::get('/my-reports', [ReportController::class, 'myReports']);
    
});