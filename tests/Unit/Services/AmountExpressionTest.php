<?php

namespace Tests\Unit\Services;

use App\Services\AmountExpression;
use PHPUnit\Framework\TestCase;

class AmountExpressionTest extends TestCase
{
    public function test_plain_amounts_and_expressions_evaluate_to_non_negative_money(): void
    {
        $calculator = new AmountExpression;

        $this->assertSame('15.00', $calculator->evaluate('10+5', '1.234,56'));
        $this->assertSame('15.00', $calculator->evaluate('10 + 5', '1.234,56'));
        $this->assertSame('21.00', $calculator->evaluate('10*2+1', '1.234,56'));
        $this->assertSame('5.00', $calculator->evaluate('20-5*3', '1.234,56'));
        $this->assertSame('2.50', $calculator->evaluate('10/4', '1.234,56'));
        $this->assertSame('14.00', $calculator->evaluate('(10+4)*1', '1.234,56'));
        $this->assertSame('21.00', $calculator->evaluate('10,50*2', '1.234,56'));
        $this->assertSame('21.00', $calculator->evaluate('10.50*2', '1,234.56'));
        $this->assertSame('20.50', $calculator->evaluate('20,50', '1.234,56'));
        $this->assertSame('-2.50', $calculator->evaluate('-2,50', '1.234,56'));
        $this->assertSame('0.00', $calculator->evaluate('5-5', '1.234,56'));
    }

    public function test_negative_results_and_invalid_expressions_are_rejected(): void
    {
        $calculator = new AmountExpression;

        $this->assertNull($calculator->evaluate('10-20', '1.234,56'));
        $this->assertNull($calculator->evaluate('10/0', '1.234,56'));
        $this->assertNull($calculator->evaluate('10++5', '1.234,56'));
        $this->assertNull($calculator->evaluate('abc', '1.234,56'));
        $this->assertNull($calculator->evaluate('(10+5', '1.234,56'));
        $this->assertNull($calculator->evaluate('10+', '1.234,56'));
    }
}
