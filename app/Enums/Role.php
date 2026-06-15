<?php

namespace App\Enums;

enum Role: string
{
    case Admin      = 'admin';
    case Doctor     = 'doctor';
    case Technician = 'technician';

    public function label(): string
    {
        return __('app.roles.' . $this->value);
    }
}
