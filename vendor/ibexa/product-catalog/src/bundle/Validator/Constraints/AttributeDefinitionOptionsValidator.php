<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Validator\Constraints;

use Ibexa\ProductCatalog\Local\Repository\Attribute\OptionsValidatorRegistryInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinitionOptions;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class AttributeDefinitionOptionsValidator extends ConstraintValidator
{
    private OptionsValidatorRegistryInterface $registry;

    public function __construct(OptionsValidatorRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param mixed $value
     * @param \Ibexa\Bundle\ProductCatalog\Validator\Constraints\AttributeDefinitionOptions $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!is_array($value)) {
            return;
        }

        $identifier = $constraint->type->getIdentifier();
        if (!$this->registry->hasValidator($identifier)) {
            return;
        }

        $errors = $this->registry->getValidator($identifier)->validateOptions(new AttributeDefinitionOptions($value));
        foreach ($errors as $error) {
            $this->context
                ->buildViolation($error->getMessage())
                ->atPath($error->getTarget() ?? '')
                ->setParameters($error->getParameters())
                ->addViolation();
        }
    }
}
