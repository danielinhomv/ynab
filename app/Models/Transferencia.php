<?php

namespace App\Models;

use Database\Factories\TransferenciaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'from_cuenta_id',
    'to_cuenta_id',
    'monto_origen',
    'monto_destino',
    'moneda_origen',
    'moneda_destino',
    'tipo_cambio',
    'anio',
    'mes',
    'fecha',
])]
class Transferencia extends Model
{
    /** @use HasFactory<TransferenciaFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Cuenta, $this>
     */
    public function fromCuenta(): BelongsTo
    {
        return $this->belongsTo(Cuenta::class, 'from_cuenta_id');
    }

    /**
     * @return BelongsTo<Cuenta, $this>
     */
    public function toCuenta(): BelongsTo
    {
        return $this->belongsTo(Cuenta::class, 'to_cuenta_id');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'monto_origen' => 'decimal:2',
            'monto_destino' => 'decimal:2',
            'tipo_cambio' => 'decimal:8',
            'fecha' => 'date',
        ];
    }
}
