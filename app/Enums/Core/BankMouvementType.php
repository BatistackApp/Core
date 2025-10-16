<?php

declare(strict_types=1);

namespace App\Enums\Core;

enum BankMouvementType: string
{
    case CARD = 'card';
    case CHECK = 'check';
    case TRANSFER = 'transfer';
    case DIRECT_DEBIT = 'direct_debit';
    case DEPOSIT = 'deposit';
    case WITHDRAWAL = 'withdrawal';
    case DEFERRED_DEBIT_CARD = 'deferred_debit_card';
    case UNKNOWN = 'unknown';

    public function label(): string
    {
        return match ($this) {
            self::CARD => 'Carte',
            self::CHECK => 'Chèque',
            self::TRANSFER => 'Virement',
            self::DIRECT_DEBIT => 'Prélèvement Bancaire',
            self::DEPOSIT => 'Dépot',
            self::WITHDRAWAL => 'Retrait',
            self::DEFERRED_DEBIT_CARD => 'Débit Différé',
            self::UNKNOWN => 'INCONNUE',
        };
    }
}
