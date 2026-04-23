<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Routes for the Flutter App
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});