<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Product;

use Countable;
use IteratorAggregate;

/**
 * @extends IteratorAggregate<\Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface>
 */
interface ProductVariantListInterface extends IteratorAggregate, Countable
{
    /**
     * Partial list of variants.
     *
     * @return \Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface[]
     */
    public function getVariants(): array;

    /**
     * Return total count of found variants (filtered by permissions).
     */
    public function getTotalCount(): int;
}
