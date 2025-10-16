<?php

namespace App\Models\Comptabilite;

use Illuminate\Database\Eloquent\Model;

class PlanComptable extends Model
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
