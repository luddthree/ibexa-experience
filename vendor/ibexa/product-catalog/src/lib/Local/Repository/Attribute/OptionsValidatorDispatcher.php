<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Attribute;

use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinitionOptions;

/**
 * @internal
 */
final class OptionsValidatorDispatcher
{
    private OptionsValidatorRegistryInterface $registry;

    public function __construct(OptionsValidatorRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param array<string,mixed> $options
     *
     * @return \Ibexa\Contracts\ProductCatalog\Local\Attribute\OptionsValidatorError[]
     */
    public function validateOptions(AttributeTypeInterface $type, array $options): array
    {
        $typeIdentifier = $type->getIdentifier();
        if ($this->registry->hasValidator($typeIdentifier)) {
            return $this->registry->getValidator($typeIdentifier)->validateOptions(
                new AttributeDefinitionOptions($options)
            );
        }

        return [];
    }
}
