<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\AssignAttributeDefinitionToolbar\Values;

use IteratorAggregate;
use Traversable;

/**
 * @implements \IteratorAggregate<\Ibexa\Bundle\ProductCatalog\UI\AssignAttributeDefinitionToolbar\Values\ToolbarGroup>
 */
final class Toolbar implements IteratorAggregate
{
    /** @var \Ibexa\Bundle\ProductCatalog\UI\AssignAttributeDefinitionToolbar\Values\ToolbarGroup[] */
    private iterable $groups;

    /**
     * @param \Ibexa\Bundle\ProductCatalog\UI\AssignAttributeDefinitionToolbar\Values\ToolbarGroup[] $groups
     */
    public function __construct(iterable $groups)
    {
        $this->groups = $groups;
    }

    /**
     * @return \Ibexa\Bundle\ProductCatalog\UI\AssignAttributeDefinitionToolbar\Values\ToolbarGroup[]
     */
    public function getGroups(): iterable
    {
        return $this->groups;
    }

    /**
     * @return \Iterator<\Ibexa\Bundle\ProductCatalog\UI\AssignAttributeDefinitionToolbar\Values\ToolbarGroup>
     */
    public function getIterator(): Traversable
    {
        yield from $this->groups;
    }
}
