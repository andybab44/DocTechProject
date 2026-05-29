<?php

namespace App\Notifications;

use App\Models\License;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LicenseExpiringNotification extends Notification
{
    public function __construct(private readonly License $license) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $daysLeft = (int) now()->startOfDay()->diffInDays($this->license->expires_at->startOfDay());

        return (new MailMessage)
            ->subject('Your DocTech license expires in ' . $daysLeft . ' day(s)')
            ->greeting('Hello, ' . $notifiable->name . '!')
            ->line('Your DocTech license is set to expire on ' . $this->license->expires_at->toFormattedDateString() . '.')
            ->line('Please contact your administrator to renew your license before it expires.')
            ->line('Once expired, access to licensed modules will be restricted.');
    }
}
