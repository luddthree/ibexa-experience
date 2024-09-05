<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Product;

use Countable;
use Ibexa\Contracts\Core\Repository\Collections\TotalCountAwareInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResultCollection;
use IteratorAggregate;

/**
 * @extends IteratorAggregate<\Ibexa\Contracts\ProductCatalog\Values\ProductInterface>
 */
interface ProductListInterface extends IteratorAggregate, Countable, TotalCountAwareInterface
{
    /**
     * Partial list of products.
     *
     * @return \Ibexa\Contracts\ProductCatalog\Values\ProductInterface[]
     */
    public function getProducts(): array;

    /**
     * Return total count of found products (filtered by permissions).
     */
    public function getTotalCount(): int;

    public function getAggregations(): ?AggregationResultCollection;
}
