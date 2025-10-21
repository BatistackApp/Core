<?php

declare(strict_types=1);

namespace App\Models\Comptabilite;

use Illuminate\Database\Eloquent\Model;

final class PlanComptable extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'lettrage' => 'boolean',
        ];
    }
}
