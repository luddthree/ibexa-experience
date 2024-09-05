<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

final class ProductBulkDeleteData
{
    /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductInterface[] */
    private array $products;

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\ProductInterface[] $products
     */
    public function __construct(array $products = [])
    {
        $this->products = $products;
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\ProductInterface[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\ProductInterface[] $products
     */
    public function setProducts(array $products): void
    {
        $this->products = $products;
    }
}
