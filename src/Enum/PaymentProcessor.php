<?php

declare(strict_types=1);

namespace App\Enum;

enum PaymentProcessor: string
{
    case PayPal = 'paypal';
    case Stripe = 'stripe';

    public function label(): string
    {
        return match ($this) {
            self::PayPal => 'PayPal',
            self::Stripe => 'Stripe',
        };
    }

    public static function values(): array
    {
        return array_map(fn(self $case): string => $case->value, self::cases());
    }
}