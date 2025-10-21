<?php

namespace App\Models\Tiers;

use App\Models\Core\Bank;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TiersBank extends Model
{
    /** @use HasFactory<\Database\Factories\Tiers\TiersBankFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [];

    public function tiers(): BelongsTo
    {
        return $this->belongsTo(Tiers::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    protected function casts(): array
    {
        return [
            'default' => 'boolean',
        ];
    }
}
