<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Rest\Value;

final class ProductVariantList extends Value
{
    /**
     * @var \Ibexa\Bundle\ProductCatalog\REST\Value\ProductVariant[]
     */
    public array $productVariants = [];

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\ProductVariant[] $productVariants
     */
    public function __construct(array $productVariants)
    {
        $this->productVariants = $productVariants;
    }
}
