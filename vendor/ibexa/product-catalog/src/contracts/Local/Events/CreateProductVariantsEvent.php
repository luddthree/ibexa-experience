<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Events;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

final class CreateProductVariantsEvent extends AfterEvent
{
    private ProductInterface $product;

    /** @var iterable<\Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductVariantCreateStruct> */
    private iterable $createStructs;

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductVariantCreateStruct> $createStructs
     */
    public function __construct(ProductInterface $product, iterable $createStructs)
    {
        $this->product = $product;
        $this->createStructs = $createStructs;
    }

    public function getProduct(): ProductInterface
    {
        return $this->product;
    }

    /**
     * @return iterable<\Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductVariantCreateStruct>
     */
    public function getCreateStructs(): iterable
    {
        return $this->createStructs;
    }
}
