<?php

namespace App\Enums;

enum AppointmentStatus: string
{
    case Scheduled = 'scheduled';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            AppointmentStatus::Scheduled => 'Scheduled',
            AppointmentStatus::Completed => 'Completed',
            AppointmentStatus::Cancelled => 'Cancelled',
        };
    }

    public function colour(): string
    {
        return match($this) {
            AppointmentStatus::Scheduled => 'bg-blue-100 text-blue-700',
            AppointmentStatus::Completed => 'bg-green-100 text-green-700',
            AppointmentStatus::Cancelled => 'bg-red-100 text-red-600',
        };
    }
}
