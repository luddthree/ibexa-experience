<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Validator\Constraints;

use Ibexa\Bundle\ProductCatalog\Form\Data\Currency\CurrencyCreateData;
use Ibexa\Bundle\ProductCatalog\Form\Data\Currency\CurrencyUpdateData;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class UniqueCurrencyCodeValidator extends ConstraintValidator
{
    private CurrencyServiceInterface $currencyService;

    public function __construct(CurrencyServiceInterface $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * @param \Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueCurrencyCode $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$value instanceof CurrencyCreateData && !$value instanceof CurrencyUpdateData) {
            return;
        }

        if ($value->getCode() === null) {
            return;
        }

        try {
            $currency = $this->currencyService->getCurrencyByCode($value->getCode());

            if ($this->isSameCurrency($value, $currency)) {
                return;
            }

            $this->context
                ->buildViolation($constraint->message)
                ->atPath('code')
                ->setParameter('%code%', $value->getCode())
                ->addViolation();
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @param \Ibexa\Bundle\ProductCatalog\Form\Data\Currency\CurrencyCreateData|\Ibexa\Bundle\ProductCatalog\Form\Data\Currency\CurrencyUpdateData $value
     */
    private function isSameCurrency($value, CurrencyInterface $currency): bool
    {
        if ($value instanceof CurrencyUpdateData) {
            return $value->getId() === $currency->getId();
        }

        return false;
    }
}
