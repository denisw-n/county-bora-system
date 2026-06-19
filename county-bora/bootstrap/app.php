<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        /*
        |--------------------------------------------------------------------------
        | Sanctum Mobile/API Authentication FIX
        |--------------------------------------------------------------------------
        | This is REQUIRED for Flutter + Bearer token authentication
        */
        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
        ]);

        // Enable Sanctum session handling for SPA/mobile hybrid cases
        $middleware->statefulApi();

        /*
        |--------------------------------------------------------------------------
        | Custom Middleware Aliases
        |--------------------------------------------------------------------------
        */
        $middleware->alias([
            'is_verified' => \App\Http\Middleware\EnsureUserIsVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        
        // Handle Throttling Exception to prevent 500 error screen
        $exceptions->render(function (ThrottleRequestsException $e, Request $request) {
            // If it's a web request, show the 429 view
            if (!$request->expectsJson()) {
                return response()->view('errors.429', [], 429);
            }
            
            // If it's an API request, return JSON
            return response()->json([
                'status'  => 'error',
                'message' => 'Too many login attempts. Please try again later.'
            ], 429);
        });

    })
    ->create();