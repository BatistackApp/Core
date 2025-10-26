<?php

declare(strict_types=1);

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Option extends Model
{
    /** @use HasFactory<\Database\Factories\Core\OptionFactory> */
    use HasFactory;
    
    protected $guarded = [];

    private array $cast = [
        'settings' => 'array',
    ];
}
