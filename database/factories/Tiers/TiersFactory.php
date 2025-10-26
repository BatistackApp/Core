<?php

namespace Database\Factories\Tiers;

use App\Enums\Tiers\TiersNature;
use App\Enums\Tiers\TiersType;
use App\Models\Tiers\Tiers;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tiers\Tiers>
 */
class TiersFactory extends Factory
{
    protected $model = Tiers::class;

    public function definition(): array
    {
        $nature = Arr::random(TiersNature::array());
        $type = Arr::random(TiersType::array());
        $tva = $this->faker->boolean(80);

        return [
            'name' => $this->faker->company(),
            'nature' => $nature,
            'type' => $type,
            'code_tiers' => $nature === 'fournisseur' ? 'FOUR2026-'.random_int(1, 600) : 'CLT2026-'.random_int(1, 600),
            'siren' => $this->faker->numerify('#########'), // 9 chiffres pour un SIREN valide
            'tva' => $tva,
            'num_tva' => $tva ? $this->faker->numerify('FR###########') : null,
        ];
    }
}
