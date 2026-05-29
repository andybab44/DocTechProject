<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Appointment $appointment) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appointment = $this->appointment;
        $patient     = $appointment->patient;
        $scheduledAt = $appointment->scheduled_at->format('d M Y H:i');

        return (new MailMessage)
            ->subject(__('app.appointment_reminder_subject', ['patient' => $patient->name]))
            ->greeting(__('app.hello', ['name' => $notifiable->name]))
            ->line(__('app.appointment_reminder_body', [
                'patient'  => $patient->name,
                'datetime' => $scheduledAt,
            ]))
            ->action(__('app.view_appointment'), route('appointments.show', $appointment))
            ->line(__('app.appointment_reminder_footer'));
    }
}
