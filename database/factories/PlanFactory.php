<?php

namespace Database\Factories;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Plan>
 */
class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->words(2, true),
            'currency' => fake()->currencyCode(),
            'fecha' => fake()->date(),
            'number_format' => fake()->bothify('format-#'),
            'currency_placement' => fake()->lexify('placement-????'),
        ];
    }
}
