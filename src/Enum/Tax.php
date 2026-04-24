<?php

declare(strict_types=1);

namespace App\Enum;

enum Tax: string
{
    case Germany = 'DE';
    case Italy = 'IT';
    case France = 'FR';
    case Greece = 'GR';

    public function rate(): float
    {
        return match ($this) {
            self::Germany => 0.19,
            self::Italy => 0.22,
            self::France => 0.20,
            self::Greece => 0.24,
        };
    }

    public function pattern(): string
    {
        return match ($this) {
            self::Germany => 'DE\d{9}',
            self::Italy => 'IT\d{11}',
            self::France => 'FR[A-Z]{2}\d{9}',
            self::Greece => 'GR\d{9}',
        };
    }
}