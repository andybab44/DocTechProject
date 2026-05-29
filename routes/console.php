<?php

use App\Console\Commands\NotifyExpiringLicenses;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Notify users 7 days before their license expires
Schedule::command(NotifyExpiringLicenses::class)->dailyAt('08:00');
