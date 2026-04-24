<?php

declare(strict_types=1);

namespace App\Service\Payment\Adapter;

use App\Enum\PaymentProcessor;
use App\Exception\BusinessException;
use App\Service\Payment\PaymentProcessorInterface;
use App\ValueObject\Money;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;


#[AutoconfigureTag('payment.processor')]
final class StripePaymentProcessorAdapter implements PaymentProcessorInterface
{
    private StripePaymentProcessor $processor;

    public function __construct()
    {
        $this->processor = new StripePaymentProcessor();
    }

    public function process(Money $amount): void
    {
        $success = $this->processor->processPayment($amount->toFloat());

        if (!$success) {
            throw new BusinessException('Payment processing failed');
        }
    }

    public function supports(PaymentProcessor $paymentProcessor): bool
    {
        return $paymentProcessor === PaymentProcessor::Stripe;
    }
}