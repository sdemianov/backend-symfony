<?php

declare(strict_types=1);

namespace App\ValueObject;

final readonly class Money
{
    private function __construct(
        private float $amount
    ) {}

    public static function fromFloat(float $amount): self
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Amount cannot be negative');
        }

        return new self(round($amount, 2));
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function add(self $other): self
    {
        return self::fromFloat($this->amount + $other->amount);
    }

    public function subtract(self $other): self
    {
        return self::fromFloat(max(0, $this->amount - $other->amount));
    }

    public function multiply(float $multiplier): self
    {
        return self::fromFloat($this->amount * $multiplier);
    }

    public function toFloat(): float
    {
        return $this->amount;
    }

    public function toCents(): int
    {
        return (int) round($this->amount * 100);
    }
}