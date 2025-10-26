<?php

declare(strict_types=1);

namespace App\Models\Core;

use App\Enums\Core\ServiceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    /** @use HasFactory<\Database\Factories\Core\ServiceFactory> */
    use HasFactory;

    protected $guarded = [];

    private array $cast = [
        'status' => ServiceStatus::class,
    ];
}
