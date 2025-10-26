<?php
// database/factories/ServiceFactory.php
namespace Database\Factories\Core;

use App\Enums\Core\ServiceStatus;
use App\Models\Core\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition()
    {
        return [
            'service_code' => $this->faker->uuid(),
            'status' => ServiceStatus::OK->value,
            'storage_limit' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}