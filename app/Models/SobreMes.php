<?php

namespace App\Models;

use Database\Factories\SobreMesFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['sobre_id', 'anio', 'mes', 'assigned', 'activity', 'available'])]
class SobreMes extends Model
{
    /** @use HasFactory<SobreMesFactory> */
    use HasFactory;

    protected $table = 'sobre_meses';

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
            'assigned' => 'decimal:2',
            'activity' => 'decimal:2',
            'available' => 'decimal:2',
        ];
    }
}
