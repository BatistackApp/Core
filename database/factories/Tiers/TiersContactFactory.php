<?php

namespace Database\Factories\Tiers;

use App\Models\Tiers\TiersContact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tiers\TiersContact>
 */
class TiersContactFactory extends Factory
{
    protected $model = TiersContact::class;

    public function definition(): array
    {
        return [
            'nom' => $this->faker->firstName(),
            'prenom' => $this->faker->lastName(),
            'civilite' => $this->faker->title(),
            'poste' => $this->faker->jobTitle(),
            'tel' => $this->faker->phoneNumber(),
            'portable' => $this->faker->mobileNumber(),
            'email' => $this->faker->unique()->safeEmail(),
        ];
    }
}
