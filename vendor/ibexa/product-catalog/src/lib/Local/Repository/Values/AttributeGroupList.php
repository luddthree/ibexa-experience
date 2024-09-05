<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Values;

use ArrayIterator;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupListInterface;

final class AttributeGroupList implements AttributeGroupListInterface
{
    /** @var \Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup[] */
    private array $attributeGroups;

    private int $totalCount;

    /**
     * @param \Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup[] $attributeGroups
     */
    public function __construct(array $attributeGroups = [], int $totalCount = 0)
    {
        $this->attributeGroups = $attributeGroups;
        $this->totalCount = $totalCount;
    }

    /**
     * @return \Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup[]
     */
    public function getAttributeGroups(): array
    {
        return $this->attributeGroups;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    /**
     * @return \ArrayIterator<int,\Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->attributeGroups);
    }
}
