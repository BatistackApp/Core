<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class ConditionReglement extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'fdm' => 'boolean',
        ];
    }
}
