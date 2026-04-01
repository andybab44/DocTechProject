<?php

namespace App\Enums;

enum WorkJobStatus: string
{
    case Pending    = 'pending';
    case InProgress = 'in_progress';
    case Done       = 'done';
    case Cancelled  = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Pending    => 'Pending',
            self::InProgress => 'In Progress',
            self::Done       => 'Done',
            self::Cancelled  => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pending    => 'yellow',
            self::InProgress => 'blue',
            self::Done       => 'green',
            self::Cancelled  => 'red',
        };
    }
}
