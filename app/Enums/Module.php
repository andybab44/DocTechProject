<?php

namespace App\Enums;

enum Module: string
{
    case Inventory    = 'inventory';
    case Appointments = 'appointments';
    case Reviews      = 'reviews';
    case Analytics    = 'analytics';

    public function label(): string
    {
        return match($this) {
            Module::Inventory    => 'Inventory Management',
            Module::Appointments => 'Patient Appointments',
            Module::Reviews      => 'Review System',
            Module::Analytics    => 'Analytics',
        };
    }
}
