<?php

namespace App\Models\Core;

use App\Enums\Core\ServiceStatus;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $guarded = [];

    protected $casts = [
        'status' => ServiceStatus::class,
    ];
}
