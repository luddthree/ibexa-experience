<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\CatalogFilters\Attribute;

use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;

interface FilterDefinitionFactoryInterface
{
    public function createFilterDefinition(
        AttributeDefinitionInterface $attributeDefinition,
        string $identifier
    ): FilterDefinitionInterface;
}
