<?php

namespace Tests\Feature\Console;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use App\Notifications\AppointmentReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendAppointmentRemindersTest extends TestCase
{
    use RefreshDatabase;

    public function test_sends_reminder_for_appointment_in_window(): void
    {
        Notification::fake();

        $doctor  = User::factory()->doctor()->create();
        $patient = Patient::factory()->create();

        Appointment::factory()->for($doctor, 'doctor')->for($patient, 'patient')->scheduled()->create([
            'scheduled_at' => now()->addHours(24)->startOfHour(),
        ]);

        $this->artisan('appointments:send-reminders --hours=24')->assertSuccessful();

        Notification::assertSentTo($doctor, AppointmentReminderNotification::class);
    }

    public function test_does_not_send_for_appointment_outside_window(): void
    {
        Notification::fake();

        $doctor  = User::factory()->doctor()->create();
        $patient = Patient::factory()->create();

        Appointment::factory()->for($doctor, 'doctor')->for($patient, 'patient')->scheduled()->create([
            'scheduled_at' => now()->addHours(48),
        ]);

        $this->artisan('appointments:send-reminders --hours=24')->assertSuccessful();

        Notification::assertNothingSent();
    }

    public function test_does_not_send_for_cancelled_appointment(): void
    {
        Notification::fake();

        $doctor  = User::factory()->doctor()->create();
        $patient = Patient::factory()->create();

        Appointment::factory()->for($doctor, 'doctor')->for($patient, 'patient')->cancelled()->create([
            'scheduled_at' => now()->addHours(24)->startOfHour(),
        ]);

        $this->artisan('appointments:send-reminders --hours=24')->assertSuccessful();

        Notification::assertNothingSent();
    }

    public function test_outputs_count_when_reminders_sent(): void
    {
        Notification::fake();

        $doctor  = User::factory()->doctor()->create();
        $patient = Patient::factory()->create();

        Appointment::factory()->for($doctor, 'doctor')->for($patient, 'patient')->scheduled()->create([
            'scheduled_at' => now()->addHours(24)->startOfHour(),
        ]);

        $this->artisan('appointments:send-reminders --hours=24')
            ->expectsOutputToContain('1 appointment reminder')
            ->assertSuccessful();
    }
}
