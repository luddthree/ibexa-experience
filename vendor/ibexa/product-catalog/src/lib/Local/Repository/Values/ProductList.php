<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Values;

use ArrayIterator;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResultCollection;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductListInterface;
use Traversable;

final class ProductList implements ProductListInterface
{
    /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductInterface[] */
    private array $products;

    private int $total;

    private ?AggregationResultCollection $aggregations;

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\ProductInterface[] $products
     */
    public function __construct(
        array $products = [],
        int $total = 0,
        ?AggregationResultCollection $aggregations = null
    ) {
        $this->products = $products;
        $this->total = $total;
        $this->aggregations = $aggregations;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->products);
    }

    public function count(): int
    {
        return count($this->products);
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    public function getTotalCount(): int
    {
        return $this->total;
    }

    public function getAggregations(): ?AggregationResultCollection
    {
        return $this->aggregations;
    }
}
