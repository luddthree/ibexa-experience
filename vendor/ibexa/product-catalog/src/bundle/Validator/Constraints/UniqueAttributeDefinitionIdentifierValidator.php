<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Validator\Constraints;

use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeDefinitionCreateData;
use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeDefinitionUpdateData;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Checks if Attribute Definition identifier already exists in the product catalog.
 */
final class UniqueAttributeDefinitionIdentifierValidator extends ConstraintValidator
{
    private AttributeDefinitionServiceInterface $attributeDefinitionService;

    public function __construct(AttributeDefinitionServiceInterface $attributeDefinitionService)
    {
        $this->attributeDefinitionService = $attributeDefinitionService;
    }

    /**
     * @param \Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueAttributeDefinitionIdentifier $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$value instanceof AttributeDefinitionCreateData && !$value instanceof AttributeDefinitionUpdateData) {
            return;
        }

        if ($value->getIdentifier() === null) {
            return;
        }

        try {
            $attributeDefinition = $this->attributeDefinitionService->getAttributeDefinition($value->getIdentifier());

            if ($this->isOriginalIdentifier($value, $attributeDefinition)) {
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
     * @param \Ibexa\Bundle\ProductCatalog\Form\Data\AttributeDefinitionCreateData|\Ibexa\Bundle\ProductCatalog\Form\Data\AttributeDefinitionUpdateData $value
     */
    private function isOriginalIdentifier($value, AttributeDefinitionInterface $attributeDefinition): bool
    {
        if ($value instanceof AttributeDefinitionUpdateData) {
            return $value->getOriginalIdentifier() === $attributeDefinition->getIdentifier();
        }

        return false;
    }
}
