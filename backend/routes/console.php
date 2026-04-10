<?php

use App\Support\OpsAlerts;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Enterprise readiness scheduler:
// - Queue health checks every 5 minutes
// - Security data cleanup daily
Schedule::command('ops:queue-health')
    ->everyFiveMinutes()
    ->onFailure(function () {
        OpsAlerts::queueHealthDegraded();
    });
Schedule::command('ops:cleanup-security-data')->dailyAt('02:15');
Schedule::command('auth:clear-resets')->dailyAt('02:30');
