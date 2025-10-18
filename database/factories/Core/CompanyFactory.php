<?php

declare(strict_types=1);

namespace Database\Factories\Core;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Core\Company>
 */
final class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'address' => fake()->streetAddress(),
            'code_postal' => fake()->postcode(),
            'ville' => fake()->city(),
            'pays' => fake()->country(),
            'phone' => fake()->e164PhoneNumber(),
            'email' => fake()->safeEmail(),
        ];
    }
}
