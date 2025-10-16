<?php

namespace App\Models\Core;

use App\Enums\Core\BankStatus;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
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
