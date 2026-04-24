<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Enum\PaymentProcessor;
use App\ValueObject\Money;

interface PaymentProcessorInterface
{
    public function process(Money $amount): void;
    public function supports(PaymentProcessor $paymentProcessor): bool;
}