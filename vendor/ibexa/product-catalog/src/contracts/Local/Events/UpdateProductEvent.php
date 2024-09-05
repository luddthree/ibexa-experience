<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Events;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

final class UpdateProductEvent extends AfterEvent
{
    private ProductInterface $product;

    private ProductUpdateStruct $updateStruct;

    public function __construct(ProductInterface $product, ProductUpdateStruct $updateStruct)
    {
        $this->product = $product;
        $this->updateStruct = $updateStruct;
    }

    public function getProduct(): ProductInterface
    {
        return $this->product;
    }

    public function getUpdateStruct(): ProductUpdateStruct
    {
        return $this->updateStruct;
    }
}
