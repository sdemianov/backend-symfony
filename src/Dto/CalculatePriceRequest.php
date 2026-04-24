<?php

declare(strict_types=1);

namespace App\Dto;

use App\Validator\Constraint\TaxNumber;
use Symfony\Component\Validator\Constraints as Assert;

class CalculatePriceRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Product ID is required')]
        #[Assert\Positive(message: 'Product ID must be positive')]
        public ?int $product = null,

        #[Assert\NotBlank(message: 'Tax number is required')]
        #[TaxNumber]
        public ?string $taxNumber = null,

        public ?string $couponCode = null,
    ) {}
}
