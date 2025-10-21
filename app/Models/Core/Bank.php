<?php

declare(strict_types=1);

namespace App\Models\Core;

use App\Enums\Core\BankStatus;
use Illuminate\Database\Eloquent\Model;

final class Bank extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'status_aggregate' => BankStatus::class,
            'status_payment' => BankStatus::class,
        ];
    }
}
