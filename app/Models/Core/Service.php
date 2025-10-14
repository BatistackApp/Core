<?php

declare(strict_types=1);

namespace App\Models\Core;

use App\Enums\Core\ServiceStatus;
use Illuminate\Database\Eloquent\Model;

final class Service extends Model
{
    protected $guarded = [];

    protected ?array $cast = [
        'status' => ServiceStatus::class,
    ];
}
