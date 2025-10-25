<?php

declare(strict_types=1);

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

final class Module extends Model
{

    protected $appends = ['name_formated'];
    
    protected $guarded = [];

    private array $cast = [
        'is_active' => 'boolean',
    ];

    public function getNameFormatedAttribute(): string
    {
        return Str::replace('Module', '', $this->name);
    }
}
