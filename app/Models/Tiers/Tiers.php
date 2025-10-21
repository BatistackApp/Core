<?php

namespace App\Models\Tiers;

use App\Enums\Tiers\TiersNature;
use App\Enums\Tiers\TiersType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tiers extends Model
{
    /** @use HasFactory<\Database\Factories\Tiers\TiersFactory> */
    use HasFactory;

    protected $guarded = [];
    public $timestamps = false;

    public function addresses(): HasMany
    {
        return $this->hasMany(TiersAddress::class);
    }

    public function getNextId(): int|float
    {
        return $this->id ? $this->id + 1 : 1;
    }

    public function getNextClientCode(): string
    {
        $cus = 'CLT'.now()->year.'-';

        return $this->id ? $cus.$this->id + 1 : $cus.'1';
    }

    public function getNextFournisseurCode(): string
    {
        $cus = 'FOUR'.now()->year.'-';

        return $this->id ? $cus.$this->id + 1 : $cus.'1';
    }

    protected function casts(): array
    {
        return [
            'tva' => 'boolean',
            'nature' => TiersNature::class,
            'type' => TiersType::class,
        ];
    }
}
