<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\NameSchema;

use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;

interface NameSchemaStrategyInterface
{
    /**
     * @param mixed $value
     */
    public function resolve(AttributeDefinitionInterface $attributeDefinition, $value, string $languageCode): string;

    /**
     * @param mixed $value
     */
    public function supports(AttributeDefinitionInterface $attributeDefinition, $value): bool;
}
