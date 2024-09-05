<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\CatalogFilter\DefinitionProvider\Attribute;

use Ibexa\Contracts\ProductCatalog\CatalogFilters\Attribute\FilterDefinitionFactoryInterface;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\ProductCatalog\Local\Repository\CatalogFilter\ProductAttribute;

final class FilterDefinitionFactory implements FilterDefinitionFactoryInterface
{
    /** @phpstan-var class-string<\Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute<mixed>> */
    private string $criterionClass;

    /**
     * @phpstan-param class-string<\Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute<mixed>> $criterionClass
     */
    public function __construct(string $criterionClass)
    {
        $this->criterionClass = $criterionClass;
    }

    public function createFilterDefinition(
        AttributeDefinitionInterface $attributeDefinition,
        string $identifier
    ): FilterDefinitionInterface {
        return new ProductAttribute($attributeDefinition, $identifier, $this->criterionClass);
    }
}
