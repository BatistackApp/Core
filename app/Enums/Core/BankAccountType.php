<?php

declare(strict_types=1);

namespace App\Enums\Core;

enum BankAccountType: string
{
    case CHECKING = 'checking';
    case SAVINGS = 'savings';
    case BROKERAGE = 'brokerage';
    case CARD = 'card';
    case LOAN = 'loan';
    case PEA = 'pea';
    case LIFE_INSURANCE = 'life_insurance';
    case UNKNOWN = 'unknown';

    public function label(): string
    {
        return match ($this) {
            BankAccountType::CHECKING => 'Compte Courant',
            BankAccountType::SAVINGS => 'Livret',
            BankAccountType::BROKERAGE => 'Courtage',
            BankAccountType::CARD => 'Carte Bancaire',
            BankAccountType::LOAN => 'Prêt',
            BankAccountType::PEA => 'PEA',
            BankAccountType::LIFE_INSURANCE => 'Assurance Vie',
            BankAccountType::UNKNOWN => 'Inconnue',
        };
    }

    public function color(): string
    {
        return match ($this) {
            BankAccountType::CHECKING => 'success',
            BankAccountType::SAVINGS, BankAccountType::PEA => 'primary',
            BankAccountType::BROKERAGE, BankAccountType::LOAN, BankAccountType::LIFE_INSURANCE => 'warning',
            BankAccountType::CARD => 'danger',
            BankAccountType::UNKNOWN => 'secondary',
            default => 'gray',
        };
    }
}
