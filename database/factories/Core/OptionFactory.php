<?php
// database/factories/OptionFactory.php
namespace Database\Factories\Core;

use App\Models\Core\Option;
use Illuminate\Database\Eloquent\Factories\Factory;

class OptionFactory extends Factory
{
    protected $model = Option::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'slug' => $this->faker->slug(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}