<?php

declare(strict_types=1);

namespace App\Enums\Core;

enum BankStatus: string
{
    case Healthy = 'healthy';
    case Degraded = 'degraded';
    case Down = 'down';

    public function label(): string
    {
        return match ($this) {
            self::Healthy => 'Ok',
            self::Degraded => 'Problème connue',
            self::Down => 'Echec'
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Healthy => 'success',
            self::Degraded => 'amber-500',
            self::Down => 'danger',
            default => 'gray',
        };
    }
}
