<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\ProductCatalog\Values\Product\ProductVariantListInterface;
use Ibexa\Rest\Value;

final class ProductVariantView extends Value
{
    private string $identifier;

    private ProductVariantListInterface $productVariantList;

    public function __construct(
        string $identifier,
        ProductVariantListInterface $productVariantList
    ) {
        $this->identifier = $identifier;
        $this->productVariantList = $productVariantList;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getProductVariantList(): ProductVariantListInterface
    {
        return $this->productVariantList;
    }
}
