<?php

namespace Database\Factories\Core;

use App\Enums\Core\ServiceStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Core\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "service_code" => "SRV-".fake()->date(format: 'Ymd')."-".fake()->randomNumber(nbDigits: 4),
            "status" => ServiceStatus::OK,
            "max_user" => fake()->randomNumber(nbDigits: 2),
            "storage_limit" => fake()->randomNumber(nbDigits: 2),
        ];
    }
}
