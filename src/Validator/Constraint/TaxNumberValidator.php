<?php

declare(strict_types=1);

namespace App\Validator\Constraint;

use App\Exception\BusinessException;
use App\Service\TaxService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class TaxNumberValidator extends ConstraintValidator
{
    public function __construct(
        private readonly TaxService $taxService
    ) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof TaxNumber) {
            throw new UnexpectedTypeException($constraint, TaxNumber::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if (!$this->taxService->isValidTaxNumber($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->setCode('INVALID_TAX_NUMBER')
                ->addViolation();
        }
    }
}