<?php

namespace App\Models;

use Database\Factories\SobreFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['category_id', 'name', 'assigned', 'activity', 'available'])]
class Sobre extends Model
{
    /** @use HasFactory<SobreFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return HasMany<SobreMes, $this>
     */
    public function meses(): HasMany
    {
        return $this->hasMany(SobreMes::class);
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
