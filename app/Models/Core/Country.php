<?php

declare(strict_types=1);

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    /** @use HasFactory<\Database\Factories\Core\CountryFactory> */
    use HasFactory;

   public $timestamps = false;

    protected $guarded = [];
}
