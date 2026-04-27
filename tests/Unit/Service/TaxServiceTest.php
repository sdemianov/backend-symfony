<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Exception\BusinessException;
use App\Service\TaxService;
use App\ValueObject\Money;
use PHPUnit\Framework\TestCase;

class TaxServiceTest extends TestCase
{
    private TaxService $taxService;

    protected function setUp(): void
    {
        $this->taxService = new TaxService();
    }

    public function testGetTaxRateForGermany(): void
    {
        $rate = $this->taxService->getTaxRate('DE123456789');

        $this->assertEquals(0.19, $rate);
    }

    public function testGetTaxRateForItaly(): void
    {
        $rate = $this->taxService->getTaxRate('IT12345678900');

        $this->assertEquals(0.22, $rate);
    }

    public function testGetTaxRateForFrance(): void
    {
        $rate = $this->taxService->getTaxRate('FRAB123456789');

        $this->assertEquals(0.20, $rate);
    }

    public function testGetTaxRateForGreece(): void
    {
        $rate = $this->taxService->getTaxRate('GR123456789');

        $this->assertEquals(0.24, $rate);
    }

    public function testGetTaxRateThrowsExceptionForUnknownCountry(): void
    {
        $this->expectException(BusinessException::class);
        $this->expectExceptionMessage('Country with code "XX" not found');

        $this->taxService->getTaxRate('XX123456789');
    }

    public function testGetTaxRateThrowsExceptionForInvalidFormat(): void
    {
        $this->expectException(BusinessException::class);
        $this->expectExceptionMessage('Invalid tax number format for country DE');

        $this->taxService->getTaxRate('DE123');
    }

    public function testApplyTax(): void
    {
        $price = Money::fromFloat(100.00);
        $result = $this->taxService->applyTax($price, 'DE123456789');

        $this->assertEquals(119.00, $result->getAmount());
    }

    public function testIsValidTaxNumber(): void
    {
        $this->assertTrue($this->taxService->isValidTaxNumber('DE123456789'));
        $this->assertTrue($this->taxService->isValidTaxNumber('IT12345678900'));
        $this->assertFalse($this->taxService->isValidTaxNumber('XX123456789'));
        $this->assertFalse($this->taxService->isValidTaxNumber('DE123'));
    }
}