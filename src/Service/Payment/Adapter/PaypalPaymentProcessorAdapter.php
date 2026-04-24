<?php

declare(strict_types=1);

namespace App\Service\Payment\Adapter;

use App\Enum\PaymentProcessor;
use App\Exception\BusinessException;
use App\Service\Payment\PaymentProcessorInterface;
use App\ValueObject\Money;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;


#[AutoconfigureTag('payment.processor')]
final class PaypalPaymentProcessorAdapter implements PaymentProcessorInterface
{
    private PaypalPaymentProcessor $processor;

    public function __construct()
    {
        $this->processor = new PaypalPaymentProcessor();
    }

    public function process(Money $amount): void
    {
        try {
            $this->processor->pay($amount->toCents());
        } catch (\Exception $e) {
            throw new BusinessException($e->getMessage());
        }
    }

    public function supports(PaymentProcessor $paymentProcessor): bool
    {
        return $paymentProcessor === PaymentProcessor::PayPal;
    }
}