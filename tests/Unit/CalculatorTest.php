<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\Calculator;
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
}
