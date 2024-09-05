<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

final class ProductUpdateData extends AbstractProductData
{
    private ProductInterface $product;

    public function __construct(ProductInterface $product)
    {
        parent::__construct();

        $this->product = $product;
    }

    public function getProduct(): ProductInterface
    {
        return $this->product;
    }

    public function getOriginalCode(): string
    {
        return $this->product->getCode();
    }

    public function isNew(): bool
    {
        return false;
    }
}
