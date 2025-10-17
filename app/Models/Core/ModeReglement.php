<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class ModeReglement extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'bridgeable' => 'boolean',
            'type_paiement' => 'array',
        ];
    }
}
