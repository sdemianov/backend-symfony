<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Coupon;
use App\Repository\CouponRepository;
use App\ValueObject\Money;

final readonly class PricingService
{
    public function __construct(
        private CouponRepository $couponRepository,
        private TaxService $taxService
    ) {}

    public function calculateFinalPrice(
        ?float $basePrice,
        string $taxNumber,
        ?string $couponCode
    ): Money {
        $price = Money::fromFloat($basePrice);

        if ($couponCode !== null) {
            $price = $this->applyCoupon($price, $couponCode);
        }

        return $this->taxService->applyTax($price, $taxNumber);
    }

    private function applyCoupon(Money $price, string $couponCode): Money
    {
        $coupon = $this->couponRepository->findOneByCodeOrFail($couponCode);

        return match ($coupon->getType()) {
            Coupon::TYPE_FIXED => $price->subtract(
                Money::fromFloat($coupon->getValueAsFloat())
            ),
            Coupon::TYPE_PERCENT => $price->multiply(
                (100 - $coupon->getValueAsFloat()) / 100
            ),
        };
    }
}