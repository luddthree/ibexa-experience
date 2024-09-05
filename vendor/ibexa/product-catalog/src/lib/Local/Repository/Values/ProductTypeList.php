<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Values;

use ArrayIterator;
use Ibexa\Contracts\ProductCatalog\Values\ProductType\ProductTypeListInterface;

final class ProductTypeList implements ProductTypeListInterface
{
    /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface[] */
    private array $items;

    private int $totalCount;

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface[] $items
     */
    public function __construct(array $items = [], int $totalCount = 0)
    {
        $this->items = $items;
        $this->totalCount = $totalCount;
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface[]
     */
    public function getProductTypes(): array
    {
        return $this->items;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    /**
     * @return \ArrayIterator<int, \Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }
}
