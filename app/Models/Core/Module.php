<?php

declare(strict_types=1);

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

final class Module extends Model
{
    protected $guarded = [];

    protected ?array $cast = [
        'is_active' => 'boolean',
    ];
}
