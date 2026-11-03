<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\Calculator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    private Calculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new Calculator;
    }

    public function test_add_returns_sum(): void
    {
        $this->assertSame(5.0, $this->calculator->add(2.0, 3.0));
    }

    public function test_subtract_returns_difference(): void
    {
        $this->assertSame(3.0, $this->calculator->subtract(5.0, 2.0));
    }

    public function test_multiply_returns_product(): void
    {
        $this->assertSame(12.0, $this->calculator->multiply(3.0, 4.0));
    }

    public function test_divide_returns_quotient(): void
    {
        $this->assertSame(2.5, $this->calculator->divide(5.0, 2.0));
    }

    public function test_divide_by_zero_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Division by zero is not allowed.');

        $this->calculator->divide(10.0, 0.0);
    }
}
