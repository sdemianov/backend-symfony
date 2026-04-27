<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Enum\PaymentProcessor;
use App\Service\Payment\Adapter\StripePaymentProcessorAdapter;
use App\Service\Payment\Adapter\PaypalPaymentProcessorAdapter;
use App\Service\Payment\PaymentProcessorService;
use PHPUnit\Framework\TestCase;

class PaymentProcessorServiceTest extends TestCase
{
    private PaymentProcessorService $provider;

    protected function setUp(): void
    {
        $this->provider = new PaymentProcessorService([
            new PaypalPaymentProcessorAdapter(),
            new StripePaymentProcessorAdapter(),
        ]);
    }

    public function testGetPaypalProcessor(): void
    {
        $processor = $this->provider->getProcessor(PaymentProcessor::PayPal);

        $this->assertInstanceOf(PaypalPaymentProcessorAdapter::class, $processor);
    }

    public function testGetStripeProcessor(): void
    {
        $processor = $this->provider->getProcessor(PaymentProcessor::Stripe);

        $this->assertInstanceOf(StripePaymentProcessorAdapter::class, $processor);
    }

    public function testThrowsExceptionForUnknownProcessor(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $provider = new PaymentProcessorService([]);
        $provider->getProcessor(PaymentProcessor::PayPal);
    }
}