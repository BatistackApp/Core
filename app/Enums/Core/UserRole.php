<?php

declare(strict_types=1);

namespace App\Enums\Core;

enum UserRole: string
{
    case ADMINISTRATEUR = 'admin';
    case CLIENT = 'client';
    case FOURNISSEUR = 'fournisseur';
    case SALARIE = 'salarie';
    case COMPTABILITE = 'comptabilite';
    case COUNTERMASTER = 'Chef de Chantier';

    public function label(): string
    {
        return match ($this) {
            self::ADMINISTRATEUR => 'Administration',
            self::CLIENT => 'Client',
            self::FOURNISSEUR => 'Fournisseur',
            self::SALARIE => 'Salarié',
            self::COMPTABILITE => 'Comptabilite',
            self::COUNTERMASTER => 'Chef de Chantier',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ADMINISTRATEUR => 'danger',
            self::CLIENT => 'success',
            self::FOURNISSEUR => 'warning',
            self::SALARIE => 'info',
            self::COMPTABILITE => 'primary',
            self::COUNTERMASTER => 'secondary',
            default => 'gray',
        };
    }
}
