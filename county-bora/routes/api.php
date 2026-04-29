<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReportController;
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

    // --- Verified Citizen Only Routes ---
    // This uses the alias we created in bootstrap/app.php
    Route::middleware('is_verified')->group(function () {
        // Only verified users can submit new incident reports
        Route::post('/reports', [ReportController::class, 'store']);
    });
    
    // Any logged-in user can still see their own report history
    Route::get('/my-reports', [ReportController::class, 'myReports']);
    
});