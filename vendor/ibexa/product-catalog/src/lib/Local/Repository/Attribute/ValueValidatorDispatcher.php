<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Attribute;

use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;

/**
 * @internal
 */
final class ValueValidatorDispatcher
{
    private ValueValidatorRegistryInterface $registry;

    public function __construct(ValueValidatorRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param mixed $value
     *
     * @return \Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueValidationError[]
     */
    public function validateValue(AttributeDefinitionInterface $definition, $value): iterable
    {
        $type = $definition->getType()->getIdentifier();
        if ($this->registry->hasValidator($type)) {
            return $this->registry->getValidator($type)->validateValue($definition, $value);
        }

        return [];
    }
}
