<?php

namespace Database\Factories;

use App\Models\Cuenta;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cuenta>
 */
class CuentaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'plan_id' => Plan::factory(),
            'type' => 'indefinida',
            'name' => fake()->words(2, true),
            'money_source' => fake()->words(3, true),
            'balance' => '0.00',
        ];
    }
}
