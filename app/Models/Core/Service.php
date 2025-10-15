<?php

declare(strict_types=1);

namespace App\Models\Core;

use App\Enums\Core\ServiceStatus;
use Illuminate\Database\Eloquent\Model;

final class Service extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    protected $guarded = [];

    private ?array $cast = [
        'status' => ServiceStatus::class,
    ];
}
