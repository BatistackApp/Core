<?php

declare(strict_types=1);

namespace App\Enums\Core;

enum ServiceStatus: string
{
    case EXPIRED = 'expired';
    case OK = 'ok';
    case PENDING = 'pending';
    case UNPAID = 'unpaid';
    case ERROR = 'error';
    case SUSPENDED = 'suspended';

    public function label(): string
    {
        return match ($this) {
            self::EXPIRED => 'Expiré',
            self::OK => 'Actif',
            self::PENDING => 'En attente',
            self::UNPAID => 'Non payé',
            self::ERROR => 'Erreur',
            self::SUSPENDED => 'Suspendu',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::EXPIRED => 'danger',
            self::OK => 'success',
            self::PENDING => 'warning',
            self::UNPAID => 'danger',
            self::ERROR => 'danger',
            self::SUSPENDED => 'warning',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::EXPIRED => 'heroicon-o-x-circle',
            self::OK => 'heroicon-o-check-circle',
            self::PENDING => 'heroicon-o-arrow-path',
            self::UNPAID => 'heroicon-o-exclamation-circle',
            self::ERROR => 'heroicon-o-x-circle',
            self::SUSPENDED => 'heroicon-o-pause-circle',
        };
    }

    public function badge(): string
    {
        return "<div class='badge badge-".$this->color()."'>".$this->label().'</div>';
    }
}
