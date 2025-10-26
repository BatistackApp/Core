<?php
// database/factories/BankFactory.php
namespace Database\Factories\Core;

use App\Models\Core\Bank;
use Illuminate\Database\Eloquent\Factories\Factory;

class BankFactory extends Factory
{
    protected $model = Bank::class;

    public function definition()
    {
        return [
            'bridge_id' => $this->faker->numberBetween(1, 1000),
            'name' => $this->faker->company(),
            'logo_bank' => $this->faker->imageUrl(),
            'status_aggregation' => 'healthy',
            'status_payment' => 'healthy',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}