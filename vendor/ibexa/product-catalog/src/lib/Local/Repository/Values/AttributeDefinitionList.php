<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Values;

use ArrayIterator;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionListInterface;
use Traversable;

final class AttributeDefinitionList implements AttributeDefinitionListInterface
{
    /** @var \Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition[] */
    private array $attributeDefinitions;

    private int $totalCount;

    /**
     * @param \Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition[] $attributeDefinitions
     */
    public function __construct(array $attributeDefinitions = [], int $totalCount = 0)
    {
        $this->attributeDefinitions = $attributeDefinitions;
        $this->totalCount = $totalCount;
    }

    /**
     * @return \Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition[]
     */
    public function getAttributeDefinitions(): array
    {
        return $this->attributeDefinitions;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    /**
     * @return \ArrayIterator<int,\Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->attributeDefinitions);
    }
}
