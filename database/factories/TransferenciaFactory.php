<?php

namespace Database\Factories;

use App\Models\Cuenta;
use App\Models\Transferencia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transferencia>
 */
class TransferenciaFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'from_cuenta_id' => Cuenta::factory(),
            'to_cuenta_id' => Cuenta::factory(),
            'monto_origen' => '0.00',
            'monto_destino' => '0.00',
            'moneda_origen' => 'BOB',
            'moneda_destino' => 'BOB',
            'tipo_cambio' => '1.00000000',
            'anio' => 2026,
            'mes' => 9,
            'fecha' => '2026-09-15',
        ];
    }
}
