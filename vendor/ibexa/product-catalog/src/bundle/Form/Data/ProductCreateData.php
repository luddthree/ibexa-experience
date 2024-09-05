<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;

final class ProductCreateData extends AbstractProductData
{
    private ProductTypeInterface $productType;

    public function __construct(ProductTypeInterface $productType)
    {
        parent::__construct();

        $this->productType = $productType;
    }

    public function getProductType(): ProductTypeInterface
    {
        return $this->productType;
    }

    public function getOriginalCode(): ?string
    {
        return null;
    }

    public function isNew(): bool
    {
        return true;
    }
}
