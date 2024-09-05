<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\AssetGroup\Values;

use ArrayIterator;
use IteratorAggregate;

/**
 * @template-implements \IteratorAggregate<\Ibexa\Bundle\ProductCatalog\UI\AssetGroup\Values\AssetGroup>
 */
final class AssetGroupCollection implements IteratorAggregate
{
    /** @var \Ibexa\Bundle\ProductCatalog\UI\AssetGroup\Values\AssetGroup[] */
    private array $groups;

    /**
     * @param \Ibexa\Bundle\ProductCatalog\UI\AssetGroup\Values\AssetGroup[] $groups
     */
    public function __construct(array $groups = [])
    {
        $this->groups = $groups;
    }

    /**
     * @return \Ibexa\Bundle\ProductCatalog\UI\AssetGroup\Values\AssetGroup[]
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * @return \ArrayIterator<array-key, \Ibexa\Bundle\ProductCatalog\UI\AssetGroup\Values\AssetGroup>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->groups);
    }
}
