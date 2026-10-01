<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ExchangeRateService
{
    /**
     * @return array<string, string>
     */
    public function usdRates(): array
    {
        return Cache::remember('exchange-usd-rates', 3600, function (): array {
            $response = Http::connectTimeout(3)
                ->timeout(8)
                ->get('https://open.er-api.com/v6/latest/USD');

            if (! $response->successful() || $response->json('result') !== 'success') {
                throw new RuntimeException('No se pudo obtener el tipo de cambio.');
            }

            $rates = $response->json('rates');

            foreach (['USD', 'EUR', 'BOB'] as $code) {
                if (! isset($rates[$code]) || ! is_numeric($rates[$code])) {
                    throw new RuntimeException('No se pudo obtener el tipo de cambio.');
                }
            }

            return [
                'USD' => $this->rateString($rates['USD']),
                'EUR' => $this->rateString($rates['EUR']),
                'BOB' => $this->rateString($rates['BOB']),
            ];
        });
    }

    public function convert(string $from, string $to, string $amount): string
    {
        if ($from === $to) {
            return bcadd($amount, '0.00', 2);
        }

        $rates = $this->usdRates();

        if (! isset($rates[$from], $rates[$to])) {
            throw new RuntimeException('No se pudo obtener el tipo de cambio.');
        }

        $usd = bcdiv($amount, $rates[$from], 8);

        return $this->roundMoney(bcmul($usd, $rates[$to], 8));
    }

    public function quote(string $from, string $to): string
    {
        if ($from === $to) {
            return '1.00000000';
        }

        $rates = $this->usdRates();

        return bcdiv($rates[$to], $rates[$from], 8);
    }

    private function rateString(mixed $rate): string
    {
        return bcadd(number_format((float) $rate, 8, '.', ''), '0', 8);
    }

    private function roundMoney(string $amount): string
    {
        $negative = str_starts_with($amount, '-');
        $amount = ltrim($amount, '-');
        $rounded = bcadd($amount, '0.005', 3);

        return ($negative ? '-' : '').bcadd($rounded, '0.00', 2);
    }
}
