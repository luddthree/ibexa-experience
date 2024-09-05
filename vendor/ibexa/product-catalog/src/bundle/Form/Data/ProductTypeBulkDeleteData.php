<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

final class ProductTypeBulkDeleteData
{
    /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface[] */
    private array $productTypes;

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface[] $productTypes
     */
    public function __construct(array $productTypes = [])
    {
        $this->productTypes = $productTypes;
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface[]
     */
    public function getProductTypes(): array
    {
        return $this->productTypes;
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface[] $productTypes
     */
    public function setProductTypes(array $productTypes): void
    {
        $this->productTypes = $productTypes;
    }
}
