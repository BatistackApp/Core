<?php

namespace Database\Factories\Tiers;

use App\Models\Comptabilite\PlanComptable;
use App\Models\Core\ConditionReglement;
use App\Models\Core\ModeReglement;
use App\Models\Tiers\TiersCustomer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tiers\TiersCustomer>
 */
class TiersCustomerFactory extends Factory
{
    protected $model = TiersCustomer::class;

    public function definition(): array
    {
        return [
            'tva' => $this->faker->boolean(),
            'num_tva' => $this->faker->word(),
            'rem_relative' => random_int(0, 100),
            'rem_fixe' => random_int(0, 100),
            'mode_reglement_id' => ModeReglement::all()->random()->id,

            'code_comptable_general' => PlanComptable::all()->random()->id,
            'code_comptable_client' => PlanComptable::all()->random()->id,
            'condition_reglement_id' => ConditionReglement::all()->random()->id,
        ];
    }
}
