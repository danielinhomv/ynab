<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Sobre;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sobre>
 */
class SobreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->words(2, true),
            'assigned' => '0.00',
            'activity' => '0.00',
            'available' => '0.00',
        ];
    }
}
