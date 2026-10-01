<?php

namespace Database\Factories;

use App\Models\Cuenta;
use App\Models\Gasto;
use App\Models\Sobre;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Gasto>
 */
class GastoFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cuenta_id' => Cuenta::factory(),
            'sobre_id' => Sobre::factory(),
            'anio' => 2026,
            'mes' => 9,
            'monto' => '0.00',
        ];
    }
}
