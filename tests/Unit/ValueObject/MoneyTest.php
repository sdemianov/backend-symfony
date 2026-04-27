<?php

declare(strict_types=1);

namespace App\Tests\Unit\ValueObject;

use App\ValueObject\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function testCreate(): void
    {
        $money = Money::fromFloat(100.50);

        $this->assertEquals(100.50, $money->getAmount());
    }

    public function testCannotCreateNegative(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Money::fromFloat(-10.00);
    }

    public function testToCents(): void
    {
        $money = Money::fromFloat(100.99);

        $this->assertEquals(10099, $money->toCents());
    }

    public function testAdd(): void
    {
        $money1 = Money::fromFloat(100.00);
        $money2 = Money::fromFloat(50.50);

        $result = $money1->add($money2);

        $this->assertEquals(150.50, $result->getAmount());
    }

    public function testSubtract(): void
    {
        $money1 = Money::fromFloat(100.00);
        $money2 = Money::fromFloat(30.00);

        $result = $money1->subtract($money2);

        $this->assertEquals(70.00, $result->getAmount());
    }

    public function testSubtractCannotBeNegative(): void
    {
        $money1 = Money::fromFloat(50.00);
        $money2 = Money::fromFloat(100.00);

        $result = $money1->subtract($money2);

        $this->assertEquals(0.00, $result->getAmount());
    }

    public function testMultiply(): void
    {
        $money = Money::fromFloat(100.00);

        $result = $money->multiply(0.9);

        $this->assertEquals(90.00, $result->getAmount());
    }
}