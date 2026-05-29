<?php

namespace App\Console\Commands;

use App\Notifications\AppointmentReminderNotification;
use App\Services\AppointmentService;
use Illuminate\Console\Command;

class SendAppointmentReminders extends Command
{
    protected $signature   = 'appointments:send-reminders {--hours=24 : Hours ahead to look for appointments}';
    protected $description = 'Send reminder notifications for upcoming appointments';

    public function handle(AppointmentService $service): int
    {
        $hours        = (int) $this->option('hours');
        $appointments = $service->getDueForReminder($hours);

        if ($appointments->isEmpty()) {
            $this->info('No appointments due for reminders.');
            return self::SUCCESS;
        }

        foreach ($appointments as $appointment) {
            $appointment->doctor->notify(new AppointmentReminderNotification($appointment));
        }

        $this->info("Sent {$appointments->count()} appointment reminder(s).");

        return self::SUCCESS;
    }
}
