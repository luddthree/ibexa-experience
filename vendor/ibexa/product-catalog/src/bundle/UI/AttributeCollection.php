<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI;

use ArrayIterator;
use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Iterator;
use IteratorAggregate;

/**
 * @implements \IteratorAggregate<array-key, \Ibexa\Bundle\ProductCatalog\UI\AttributeGroup>
 */
final class AttributeCollection implements IteratorAggregate
{
    /** @var array<\Ibexa\Bundle\ProductCatalog\UI\AttributeGroup> */
    private array $groups;

    /**
     * @param \Ibexa\Bundle\ProductCatalog\UI\AttributeGroup[] $groups
     */
    public function __construct(array $groups = [])
    {
        $this->groups = $groups;
    }

    public function addAttribute(AttributeInterface $attribute): void
    {
        $group = $attribute->getAttributeDefinition()->getGroup();
        if (!array_key_exists($group->getIdentifier(), $this->groups)) {
            $this->groups[$group->getIdentifier()] = new AttributeGroup($group);
        }

        $this->groups[$group->getIdentifier()]->addAttribute($attribute);
    }

    /**
     * @return \Ibexa\Bundle\ProductCatalog\UI\AttributeGroup[]
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * @return \Iterator<array-key, \Ibexa\Bundle\ProductCatalog\UI\AttributeGroup>
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->groups);
    }

    public static function createFromProduct(ProductInterface $product): self
    {
        $collection = new AttributeCollection();
        foreach ($product->getAttributes() as $attribute) {
            $collection->addAttribute($attribute);
        }

        return $collection;
    }
}
