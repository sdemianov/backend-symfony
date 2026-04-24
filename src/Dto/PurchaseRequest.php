<?php

declare(strict_types=1);

namespace App\Dto;

use App\Enum\PaymentProcessor;
use Symfony\Component\Validator\Constraints as Assert;

final class PurchaseRequest extends CalculatePriceRequest
{
    #[Assert\NotBlank(message: 'Payment processor is required')]
    #[Assert\Choice(
        callback: [PaymentProcessor::class, 'values'],
        message: 'Payment processor {{ value }} is not valid. Valid values: {{ choices }}'
    )]
    public ?string $paymentProcessor = null;
}