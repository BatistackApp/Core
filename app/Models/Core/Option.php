<?php

declare(strict_types=1);

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

final class Option extends Model
{
    protected $guarded = [];

    private array $cast = [
        'settings' => 'array',
    ];
}
