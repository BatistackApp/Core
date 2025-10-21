<?php

namespace Database\Factories\Tiers;

use App\Models\Core\Bank;
use App\Models\Tiers\TiersBank;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tiers\TiersBank>
 */
class TiersBankFactory extends Factory
{
    protected $model = TiersBank::class;

    public function definition(): array
    {
        return [
            'iban' => $this->faker->iban('fr'),
            'bic' => $this->faker->swiftBicNumber(),
            'external_id' => null,
            'default' => $this->faker->boolean(),

            'tiers_id' => null,
            'bank_id' => Bank::all()->random()->id,
        ];
    }
}
