<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
use Laravel\Sanctum\PersonalAccessToken;
// Import these for the Rate Limiter
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * Ensure Sanctum correctly handles UUIDs for the 'tokenable_id' 
         * by using the standard PersonalAccessToken model with your 
         * string-based primary keys.
         */
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        /**
         * Rate Limiter for Login Attempts
         * Defined to resolve the MissingRateLimiterException
         */
        RateLimiter::for('login_attempts', function (Request $request) {
            // Adjust the number of attempts and decay time as needed for your presentation
            return Limit::perMinute(5, 720)->by($request->ip());
        });
    }
}