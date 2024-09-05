<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Values;

use ArrayIterator;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductVariantListInterface;
use Traversable;

final class ProductVariantList implements ProductVariantListInterface
{
    /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface[] */
    private array $items;

    private int $totalCount;

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface[] $items
     */
    public function __construct(array $items = [], int $totalCount = 0)
    {
        $this->items = $items;
        $this->totalCount = $totalCount;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface[]
     */
    public function getVariants(): array
    {
        return $this->items;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }
}
