<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $guarded = [];

    protected $casts = [
        'settings' => 'array',
    ];
}
