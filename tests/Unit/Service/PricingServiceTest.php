<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\Coupon;
use App\Exception\BusinessException;
use App\Repository\CouponRepository;
use App\Service\PricingService;
use App\Service\TaxService;
use PHPUnit\Framework\TestCase;

class PricingServiceTest extends TestCase
{
    private PricingService $pricingService;
    private CouponRepository $couponRepository;

    protected function setUp(): void
    {
        $this->couponRepository = $this->createMock(CouponRepository::class);
        $taxService = new TaxService();

        $this->pricingService = new PricingService(
            $this->couponRepository,
            $taxService
        );
    }

    public function testCalculatePriceWithoutCoupon(): void
    {
        $this->couponRepository
            ->expects($this->never())
            ->method('findOneByCodeOrFail');

        $price = $this->pricingService->calculateFinalPrice(
            100.00,
            'DE123456789',
            null
        );

        $this->assertEquals(119.00, $price->getAmount());
    }

    public function testCalculatePriceWithFixedCoupon(): void
    {
        $coupon = new Coupon();
        $coupon->setCode('D15')
            ->setType(Coupon::TYPE_FIXED)
            ->setValue(15.0);

        $this->couponRepository
            ->method('findOneByCodeOrFail')
            ->with('D15')
            ->willReturn($coupon);

        $price = $this->pricingService->calculateFinalPrice(
            100.00,
            'DE123456789',
            'D15'
        );

        $this->assertEquals(101.15, $price->getAmount());
    }

    public function testCalculatePriceWithPercentCoupon(): void
    {
        $coupon = new Coupon();
        $coupon->setCode('P10')
            ->setType(Coupon::TYPE_PERCENT)
            ->setValue(10.0);

        $this->couponRepository
            ->method('findOneByCodeOrFail')
            ->with('P10')
            ->willReturn($coupon);

        $price = $this->pricingService->calculateFinalPrice(
            100.00,
            'IT12345678900',
            'P10'
        );

        $this->assertEquals(109.80, $price->getAmount());
    }

    public function testCalculatePriceWith100PercentCoupon(): void
    {
        $coupon = new Coupon();
        $coupon->setCode('P100')
            ->setType(Coupon::TYPE_PERCENT)
            ->setValue(100.0);

        $this->couponRepository
            ->method('findOneByCodeOrFail')
            ->with('P100')
            ->willReturn($coupon);

        $price = $this->pricingService->calculateFinalPrice(
            100.00,
            'DE123456789',
            'P100'
        );

        $this->assertEquals(0.00, $price->getAmount());
    }

    public function testCalculatePriceThrowsExceptionForInvalidCoupon(): void
    {
        $this->couponRepository
            ->expects($this->once())
            ->method('findOneByCodeOrFail')
            ->with('INVALID')
            ->willThrowException(
                new BusinessException('Coupon INVALID not found', 'couponCode')
            );

        $this->expectException(BusinessException::class);
        $this->expectExceptionMessage('Coupon INVALID not found');

        $this->pricingService->calculateFinalPrice(
            100.00,
            'DE123456789',
            'INVALID'
        );
    }
}