<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Changed to 'is_verified' to match your database column name
        if ($request->user() && $request->user()->is_verified != 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Access Denied. Your account is pending identity verification by Nairobi County Admins.'
            ], 403);
        }

        return $next($request);
    }
}