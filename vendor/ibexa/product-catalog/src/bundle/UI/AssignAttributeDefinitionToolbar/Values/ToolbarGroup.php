<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\AssignAttributeDefinitionToolbar\Values;

use Iterator;
use IteratorAggregate;

/**
 * @implements \IteratorAggregate<\Ibexa\Bundle\ProductCatalog\UI\AssignAttributeDefinitionToolbar\Values\ToolbarGroupItem>
 */
final class ToolbarGroup implements IteratorAggregate
{
    private string $identifier;

    private string $name;

    /** @var \Ibexa\Bundle\ProductCatalog\UI\AssignAttributeDefinitionToolbar\Values\ToolbarGroupItem[] */
    private iterable $items;

    /**
     * @param \Ibexa\Bundle\ProductCatalog\UI\AssignAttributeDefinitionToolbar\Values\ToolbarGroupItem[] $items
     */
    public function __construct(string $identifier, string $name, iterable $items = [])
    {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->items = $items;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return \Ibexa\Bundle\ProductCatalog\UI\AssignAttributeDefinitionToolbar\Values\ToolbarGroupItem[]
     */
    public function getItems(): iterable
    {
        return $this->items;
    }

    /**
     * @return \Iterator<\Ibexa\Bundle\ProductCatalog\UI\AssignAttributeDefinitionToolbar\Values\ToolbarGroupItem>
     */
    public function getIterator(): Iterator
    {
        yield from $this->items;
    }
}
