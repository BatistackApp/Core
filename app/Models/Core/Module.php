<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $guarded = [];

    protected $cast = [
        'is_active' => 'boolean',
    ];
}
