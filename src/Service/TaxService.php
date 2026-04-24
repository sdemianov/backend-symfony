<?php

declare(strict_types=1);

namespace App\Service;

use App\Enum\Tax;
use App\Exception\BusinessException;
use App\ValueObject\Money;

final readonly class TaxService
{
    public function applyTax(Money $price, string $taxNumber): Money
    {
        return $price->multiply(1 + $this->getTaxRate($taxNumber));
    }

    public function getTaxRate(string $taxNumber): float
    {
        return $this->findTax($taxNumber)->rate();
    }

    public function isValidTaxNumber(string $taxNumber): bool
    {
        try {
            $this->findTax($taxNumber);
            return true;
        } catch (BusinessException) {
            return false;
        }
    }

    private function findTax(string $taxNumber): Tax
    {
        $countryCode = substr($taxNumber, 0, 2);
        $tax = Tax::tryFrom($countryCode);

        if ($tax === null) {
            throw new BusinessException(
                sprintf('Country with code "%s" not found', $countryCode),
                'taxNumber'
            );
        }

        if (!preg_match('/^' . $tax->pattern() . '$/', $taxNumber)) {
            throw new BusinessException(
                sprintf('Invalid tax number format for country %s', $countryCode),
                'taxNumber'
            );
        }

        return $tax;
    }
}