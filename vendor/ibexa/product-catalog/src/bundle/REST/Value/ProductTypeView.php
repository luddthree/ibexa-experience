<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\ProductCatalog\Values\ProductType\ProductTypeListInterface;
use Ibexa\Rest\Value;

final class ProductTypeView extends Value
{
    private string $identifier;

    private ProductTypeListInterface $productTypeList;

    public function __construct(string $identifier, ProductTypeListInterface $productTypeList)
    {
        $this->identifier = $identifier;
        $this->productTypeList = $productTypeList;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getProductTypeList(): ProductTypeListInterface
    {
        return $this->productTypeList;
    }
}
