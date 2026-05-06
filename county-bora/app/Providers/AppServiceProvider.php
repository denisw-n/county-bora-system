<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
use Laravel\Sanctum\PersonalAccessToken;

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
    }
}