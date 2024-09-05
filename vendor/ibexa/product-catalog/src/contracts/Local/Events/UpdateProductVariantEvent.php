<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Events;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductVariantUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface;

final class UpdateProductVariantEvent extends AfterEvent
{
    private ProductVariantInterface $productVariant;

    private ProductVariantUpdateStruct $updateStruct;

    public function __construct(
        ProductVariantInterface $productVariant,
        ProductVariantUpdateStruct $updateStruct
    ) {
        $this->productVariant = $productVariant;
        $this->updateStruct = $updateStruct;
    }

    public function getProductVariant(): ProductVariantInterface
    {
        return $this->productVariant;
    }

    public function getUpdateStruct(): ProductVariantUpdateStruct
    {
        return $this->updateStruct;
    }
}
