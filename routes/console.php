<?php

use App\Console\Commands\NotifyExpiringLicenses;
use App\Console\Commands\SendAppointmentReminders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Notify users 7 days before their license expires
Schedule::command(NotifyExpiringLicenses::class)->dailyAt('08:00');

// Send appointment reminders 24 hours before scheduled time
Schedule::command(SendAppointmentReminders::class)->hourly();
