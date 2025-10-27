<?php

namespace App\Enums\Tiers;

enum TiersNature : string
{
    case Fournisseur = 'fournisseur';
    case Client = 'client';

    public static function array(): array
    {
        return [
            'fournisseur' => self::Fournisseur->value,
            'client' => self::Client->value,
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::Fournisseur => 'Fournisseur',
            self::Client => 'Client',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Fournisseur => 'red',
            self::Client => 'green',
            default => 'gray',
        };
    }
}
