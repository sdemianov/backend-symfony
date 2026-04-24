<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Enum\PaymentProcessor;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

final readonly class PaymentProcessorService
{
    /**
     * @param iterable<PaymentProcessorInterface> $processors
     */
    public function __construct(
        #[TaggedIterator('payment.processor')]
        private iterable $processors
    ) {}

    public function getProcessor(PaymentProcessor $name): PaymentProcessorInterface
    {
        foreach ($this->processors as $processor) {
            if ($processor->supports($name)) {
                return $processor;
            }
        }

        throw new \InvalidArgumentException(
            sprintf('Payment processor "%s" not found', $name->value)
        );
    }
}