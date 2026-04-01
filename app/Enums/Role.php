<?php

namespace App\Enums;

enum Role: string
{
    case Admin      = 'admin';
    case Doctor     = 'doctor';
    case Technician = 'technician';

    public function label(): string
    {
        return match($this) {
            Role::Admin      => 'Admin',
            Role::Doctor     => 'Doctor',
            Role::Technician => 'Technician',
        };
    }
}
