<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI;

use ArrayIterator;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;
use Iterator;
use IteratorAggregate;

/**
 * @implements IteratorAggregate<array-key, \Ibexa\Contracts\ProductCatalog\Values\AttributeInterface>
 */
final class AttributeGroup implements IteratorAggregate
{
    private AttributeGroupInterface $group;

    /** @var \Ibexa\Contracts\ProductCatalog\Values\AttributeInterface[] */
    private array $attributes;

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\AttributeInterface[] $attributes
     */
    public function __construct(AttributeGroupInterface $group, array $attributes = [])
    {
        $this->group = $group;
        $this->attributes = $attributes;
    }

    public function getIdentifier(): string
    {
        return $this->group->getIdentifier();
    }

    public function getName(): string
    {
        return $this->group->getName();
    }

    public function addAttribute(AttributeInterface $attribute): void
    {
        $this->attributes[] = $attribute;
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\AttributeInterface[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return \Iterator<\Ibexa\Contracts\ProductCatalog\Values\AttributeInterface>
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->attributes);
    }
}
