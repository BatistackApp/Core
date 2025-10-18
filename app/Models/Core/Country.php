<?php

declare(strict_types=1);

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

final class Country extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    public $timestamps = false;

    protected $guarded = [];
}
