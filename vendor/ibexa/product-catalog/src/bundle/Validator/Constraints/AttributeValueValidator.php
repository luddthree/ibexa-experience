<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Validator\Constraints;

use Ibexa\ProductCatalog\Local\Repository\Attribute\ValueValidatorRegistryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class AttributeValueValidator extends ConstraintValidator
{
    private ValueValidatorRegistryInterface $registry;

    public function __construct(ValueValidatorRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param \Ibexa\Bundle\ProductCatalog\Validator\Constraints\AttributeValue $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if ($value === null) {
            return;
        }

        $definition = $constraint->definition;
        $identifier = $definition->getType()->getIdentifier();
        if (!$this->registry->hasValidator($identifier)) {
            return;
        }

        $errors = $this->registry->getValidator($identifier)->validateValue($definition, $value);
        foreach ($errors as $error) {
            $this->context
                ->buildViolation($error->getMessage())
                ->atPath($error->getTarget() ?? '')
                ->setParameters($error->getParameters())
                ->addViolation();
        }
    }
}
