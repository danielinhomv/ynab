<?php

namespace App\Services;

class AmountExpression
{
    public function evaluate(string $expression, string $numberFormat): ?string
    {
        $expression = trim($expression);

        if ($expression === '') {
            return null;
        }

        $plain = $this->normalizePlain($expression, $numberFormat);

        if ($plain !== null) {
            return $plain;
        }

        $tokens = $this->tokenize($expression, $numberFormat);

        if ($tokens === null) {
            return null;
        }

        $rpn = $this->toRpn($tokens);

        if ($rpn === null) {
            return null;
        }

        $result = $this->compute($rpn);

        if ($result === null || bccomp($result, '0', 8) < 0) {
            return null;
        }

        return $this->roundMoney($result);
    }

    private function normalizePlain(string $value, string $numberFormat): ?string
    {
        $negative = str_starts_with($value, '-');

        if ($negative) {
            $value = substr($value, 1);
        }

        if ($value === '' || str_contains($value, '-')) {
            return null;
        }

        $normalized = $this->normalizeNumber($value, $numberFormat);

        if ($normalized === null) {
            return null;
        }

        return $negative ? '-'.$normalized : $normalized;
    }

    private function normalizeNumber(string $value, string $numberFormat): ?string
    {
        if ($numberFormat === '1,234.56') {
            if (! preg_match('/^(?:\d{1,3}(?:,\d{3})*|\d+)(?:\.\d{1,2})?$/', $value)) {
                return null;
            }

            $value = str_replace(',', '', $value);
        } elseif (! preg_match('/^(?:\d{1,3}(?:\.\d{3})*|\d+)(?:,\d{1,2})?$/', $value)) {
            return null;
        } else {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        }

        [$whole, $fraction] = array_pad(explode('.', $value, 2), 2, '00');

        if ($fraction === '') {
            $fraction = '00';
        }

        if (! ctype_digit($whole) || ! ctype_digit($fraction) || strlen($fraction) > 2) {
            return null;
        }

        return $whole.'.'.str_pad($fraction, 2, '0');
    }

    /**
     * @return list<array{type: string, value: string}>|null
     */
    private function tokenize(string $expression, string $numberFormat): ?array
    {
        $tokens = [];
        $length = strlen($expression);
        $index = 0;
        $expectNumber = true;

        while ($index < $length) {
            $char = $expression[$index];

            if ($char === ' ') {
                $index++;

                continue;
            }

            if ($char === '(') {
                if (! $expectNumber) {
                    return null;
                }

                $tokens[] = ['type' => 'lparen', 'value' => '('];
                $index++;

                continue;
            }

            if ($char === ')') {
                if ($expectNumber) {
                    return null;
                }

                $tokens[] = ['type' => 'rparen', 'value' => ')'];
                $index++;
                $expectNumber = false;

                continue;
            }

            if (in_array($char, ['+', '-', '*', '/'], true)) {
                if ($expectNumber) {
                    return null;
                }

                $tokens[] = ['type' => 'op', 'value' => $char];
                $index++;
                $expectNumber = true;

                continue;
            }

            if (! ctype_digit($char)) {
                return null;
            }

            if (! $expectNumber) {
                return null;
            }

            $raw = '';

            while ($index < $length && (ctype_digit($expression[$index]) || in_array($expression[$index], ['.', ','], true))) {
                $raw .= $expression[$index];
                $index++;
            }

            $number = $this->normalizeNumber($raw, $numberFormat);

            if ($number === null) {
                return null;
            }

            $tokens[] = ['type' => 'number', 'value' => $number];
            $expectNumber = false;
        }

        if ($expectNumber || $tokens === []) {
            return null;
        }

        return $tokens;
    }

    /**
     * @param  list<array{type: string, value: string}>  $tokens
     * @return list<array{type: string, value: string}>|null
     */
    private function toRpn(array $tokens): ?array
    {
        $output = [];
        $stack = [];

        foreach ($tokens as $token) {
            if ($token['type'] === 'number') {
                $output[] = $token;

                continue;
            }

            if ($token['type'] === 'op') {
                while ($stack !== []) {
                    $top = $stack[array_key_last($stack)];

                    if ($top['type'] !== 'op' || $this->precedence($top['value']) < $this->precedence($token['value'])) {
                        break;
                    }

                    $output[] = array_pop($stack);
                }

                $stack[] = $token;

                continue;
            }

            if ($token['type'] === 'lparen') {
                $stack[] = $token;

                continue;
            }

            while ($stack !== []) {
                $top = array_pop($stack);

                if ($top['type'] === 'lparen') {
                    continue 2;
                }

                $output[] = $top;
            }

            return null;
        }

        while ($stack !== []) {
            $top = array_pop($stack);

            if ($top['type'] === 'lparen') {
                return null;
            }

            $output[] = $top;
        }

        return $output;
    }

    /**
     * @param  list<array{type: string, value: string}>  $rpn
     */
    private function compute(array $rpn): ?string
    {
        $stack = [];

        foreach ($rpn as $token) {
            if ($token['type'] === 'number') {
                $stack[] = $token['value'];

                continue;
            }

            if (count($stack) < 2) {
                return null;
            }

            $right = array_pop($stack);
            $left = array_pop($stack);
            $applied = $this->apply($token['value'], $left, $right);

            if ($applied === null) {
                return null;
            }

            $stack[] = $applied;
        }

        if (count($stack) !== 1) {
            return null;
        }

        return $stack[0];
    }

    private function apply(string $operator, string $left, string $right): ?string
    {
        return match ($operator) {
            '+' => bcadd($left, $right, 8),
            '-' => bcsub($left, $right, 8),
            '*' => bcmul($left, $right, 8),
            '/' => bccomp($right, '0', 8) === 0 ? null : bcdiv($left, $right, 8),
            default => null,
        };
    }

    private function precedence(string $operator): int
    {
        return in_array($operator, ['*', '/'], true) ? 2 : 1;
    }

    private function roundMoney(string $amount): string
    {
        $negative = str_starts_with($amount, '-');
        $amount = ltrim($amount, '-');
        $rounded = bcadd($amount, '0.005', 3);

        return ($negative ? '-' : '').bcadd($rounded, '0.00', 2);
    }
}
