<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Events;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductVariantUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface;
use UnexpectedValueException;

final class BeforeUpdateProductVariantEvent extends BeforeEvent
{
    private ProductVariantInterface $productVariant;

    private ProductVariantUpdateStruct $updateStruct;

    private ?ProductVariantInterface $resultProductVariant = null;

    public function __construct(ProductVariantInterface $productVariant, ProductVariantUpdateStruct $updateStruct)
    {
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

    public function getResultProductVariant(): ProductVariantInterface
    {
        if ($this->resultProductVariant === null) {
            $message = 'Return value is not set or not of type %s. Check hasResultProductVariant() or'
                . ' set it using setResultProductVariant() before you call the getter.';

            throw new UnexpectedValueException(sprintf($message, ProductVariantInterface::class));
        }

        return $this->resultProductVariant;
    }

    public function hasResultProductVariant(): bool
    {
        return $this->resultProductVariant instanceof ProductVariantInterface;
    }

    public function setResultProductVariant(?ProductVariantInterface $resultProductVariant): void
    {
        $this->resultProductVariant = $resultProductVariant;
    }
}
