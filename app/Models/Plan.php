<?php

namespace App\Models;

use Database\Factories\PlanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'name', 'currency', 'fecha', 'number_format', 'currency_placement'])]
class Plan extends Model
{
    /** @use HasFactory<PlanFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Category, $this>
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    /**
     * @return HasMany<Cuenta, $this>
     */
    public function cuentas(): HasMany
    {
        return $this->hasMany(Cuenta::class);
    }

    public function formatMoney(string $amount): string
    {
        return $this->formatNumber($amount, withCurrency: true);
    }

    public function amountForInput(string $amount): string
    {
        return $this->formatNumber($amount, withCurrency: false);
    }

    private function formatNumber(string $amount, bool $withCurrency): string
    {
        $negative = str_starts_with($amount, '-');
        $amount = ltrim($amount, '-');
        [$whole, $fraction] = array_pad(explode('.', $amount, 2), 2, '00');
        $fraction = str_pad(substr($fraction, 0, 2), 2, '0');

        if ($this->number_format === '1,234.56') {
            $formatted = $this->groupThousands($whole, ',').'.'.$fraction;
        } else {
            $formatted = $this->groupThousands($whole, '.').','.$fraction;
        }

        if ($withCurrency) {
            $currency = match ($this->currency) {
                'BOB' => 'Bs',
                'USD' => 'USD',
                'EUR' => 'EUR',
                default => '',
            };

            if ($currency !== '' && $this->currency_placement === 'después') {
                $formatted = $formatted.' '.$currency;
            } elseif ($currency !== '') {
                $formatted = $currency.' '.$formatted;
            }
        }

        return $negative ? '-'.$formatted : $formatted;
    }

    private function groupThousands(string $whole, string $separator): string
    {
        $result = '';
        $length = strlen($whole);

        foreach (str_split($whole) as $index => $digit) {
            $remaining = $length - $index;

            if ($index > 0 && $remaining % 3 === 0) {
                $result .= $separator;
            }

            $result .= $digit;
        }

        return $result;
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }
}
