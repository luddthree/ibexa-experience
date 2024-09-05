<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Events;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

final class CreateProductEvent extends AfterEvent
{
    private ProductCreateStruct $createStruct;

    private ProductInterface $product;

    public function __construct(ProductCreateStruct $createStruct, ProductInterface $product)
    {
        $this->createStruct = $createStruct;
        $this->product = $product;
    }

    public function getCreateStruct(): ProductCreateStruct
    {
        return $this->createStruct;
    }

    public function getProduct(): ProductInterface
    {
        return $this->product;
    }
}
