<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Attribute;

use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;

final class ValueFormatterDispatcher implements ValueFormatterDispatcherInterface
{
    private ValueFormatterRegistryInterface $registry;

    public function __construct(ValueFormatterRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function formatValue(AttributeInterface $attribute, array $parameters = []): ?string
    {
        $type = $attribute->getAttributeDefinition()->getType()->getIdentifier();
        if ($this->registry->hasFormatter($type)) {
            return $this->registry->getFormatter($type)->formatValue($attribute, $parameters);
        }

        return null;
    }
}
