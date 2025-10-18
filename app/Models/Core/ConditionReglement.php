<?php

declare(strict_types=1);

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

final class ConditionReglement extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'fdm' => 'boolean',
        ];
    }
}
