<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\StatsService;

// Keep your existing inspire command
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Add your scheduled task here
// Set to everyMinute() for testing; change back to hourly() when ready for production
Schedule::call(function () {
    $service = app(StatsService::class);
    $service->generateDepartmentalSnapshot();
    $service->refreshAnalytics();
})->daily();