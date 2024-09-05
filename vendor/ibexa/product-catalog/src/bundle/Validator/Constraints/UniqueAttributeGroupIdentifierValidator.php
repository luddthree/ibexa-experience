<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Validator\Constraints;

use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeGroupCreateData;
use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeGroupUpdateData;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Will check if Attribute Group identifier is not already used in the product catalog.
 */
final class UniqueAttributeGroupIdentifierValidator extends ConstraintValidator
{
    private AttributeGroupServiceInterface $attributeGroupService;

    public function __construct(AttributeGroupServiceInterface $attributeGroupService)
    {
        $this->attributeGroupService = $attributeGroupService;
    }

    /**
     * @param \Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueAttributeGroupIdentifier $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$value instanceof AttributeGroupCreateData && !$value instanceof AttributeGroupUpdateData) {
            return;
        }

        if ($value->getIdentifier() === null) {
            return;
        }

        try {
            $attributeGroup = $this->attributeGroupService->getAttributeGroup($value->getIdentifier());

            if ($this->isOriginalIdentifier($value, $attributeGroup)) {
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
     * @param \Ibexa\Bundle\ProductCatalog\Form\Data\AttributeGroupCreateData|\Ibexa\Bundle\ProductCatalog\Form\Data\AttributeGroupUpdateData $value
     */
    private function isOriginalIdentifier($value, AttributeGroupInterface $attributeGroup): bool
    {
        if ($value instanceof AttributeGroupUpdateData) {
            return $value->getOriginalIdentifier() === $attributeGroup->getIdentifier();
        }

        return false;
    }
}
