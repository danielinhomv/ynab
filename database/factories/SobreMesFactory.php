<?php

namespace Database\Factories;

use App\Models\Sobre;
use App\Models\SobreMes;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SobreMes>
 */
class SobreMesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sobre_id' => Sobre::factory(),
            'anio' => 2026,
            'mes' => 9,
            'assigned' => '0.00',
            'activity' => '0.00',
            'available' => '0.00',
        ];
    }
}
