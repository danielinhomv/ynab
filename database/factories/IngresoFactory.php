<?php

namespace Database\Factories;

use App\Models\Cuenta;
use App\Models\Ingreso;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ingreso>
 */
class IngresoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cuenta_id' => Cuenta::factory(),
            'fecha' => '2026-01-15',
            'origen' => fake()->words(3, true),
            'descripcion' => fake()->sentence(),
            'monto' => '0.00',
        ];
    }
}
