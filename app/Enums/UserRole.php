<?php

namespace App\Enums;

enum UserRole: string
{
    case Manager = 'manager';
    case Receptionist = 'receptionist';
    case Housekeeping = 'housekeeping';

    public function label(): string
    {
        return match ($this) {
            self::Manager => 'Manager',
            self::Receptionist => 'Receptionist',
            self::Housekeeping => 'HouseKeeping',
        };
    }
}
