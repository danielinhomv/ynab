<?php

namespace App\Models;

use Database\Factories\GastoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['cuenta_id', 'sobre_id', 'anio', 'mes', 'monto'])]
class Gasto extends Model
{
    /** @use HasFactory<GastoFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Cuenta, $this>
     */
    public function cuenta(): BelongsTo
    {
        return $this->belongsTo(Cuenta::class);
    }

    /**
     * @return BelongsTo<Sobre, $this>
     */
    public function sobre(): BelongsTo
    {
        return $this->belongsTo(Sobre::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
        ];
    }
}
