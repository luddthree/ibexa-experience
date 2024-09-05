<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Validator\Constraints;

use Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\CatalogCopyData;
use Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\CatalogCreateData;
use Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\CatalogUpdateData;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\CatalogServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class UniqueCatalogIdentifierValidator extends ConstraintValidator
{
    private CatalogServiceInterface $catalogService;

    public function __construct(CatalogServiceInterface $catalogService)
    {
        $this->catalogService = $catalogService;
    }

    /**
     * @param \Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueCatalogIdentifier $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$this->isValueSupported($value)) {
            return;
        }

        if (empty($value->getIdentifier())) {
            return;
        }

        try {
            $catalog = $this->catalogService->getCatalogByIdentifier($value->getIdentifier());

            if ($this->isSameCatalog($value, $catalog)) {
                return;
            }

            $this->context
                ->buildViolation($constraint->message)
                ->atPath('identifier')
                ->setParameter('%identifier%', $value->getIdentifier())
                ->addViolation();
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @param \Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\CatalogCreateData|\Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\CatalogUpdateData $value
     */
    private function isSameCatalog($value, CatalogInterface $catalog): bool
    {
        if ($value instanceof CatalogUpdateData) {
            return $value->getId() === $catalog->getId();
        }

        return false;
    }

    /**
     * @param mixed $value The value that should be validated
     */
    private function isValueSupported($value): bool
    {
        return $value instanceof CatalogCreateData
            || $value instanceof CatalogUpdateData
            || $value instanceof CatalogCopyData;
    }
}
