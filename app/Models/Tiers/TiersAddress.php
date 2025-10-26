<?php

namespace App\Models\Tiers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TiersAddress extends Model
{
    /** @use HasFactory<\Database\Factories\Tiers\TiersAddressFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [];

    public function tiers(): BelongsTo
    {
        return $this->belongsTo(Tiers::class);
    }
}
