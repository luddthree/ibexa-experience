<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Events;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use UnexpectedValueException;

final class BeforeUpdateProductEvent extends BeforeEvent
{
    private ProductUpdateStruct $updateStruct;

    private ?ProductInterface $resultProduct = null;

    public function __construct(ProductUpdateStruct $updateStruct)
    {
        $this->updateStruct = $updateStruct;
    }

    public function getUpdateStruct(): ProductUpdateStruct
    {
        return $this->updateStruct;
    }

    public function getResultProduct(): ProductInterface
    {
        if ($this->resultProduct === null) {
            $message = 'Return value is not set or not of type %s. Check hasResultProduct() or'
                . ' set it using setResultProduct() before you call the getter.';

            throw new UnexpectedValueException(sprintf($message, ProductInterface::class));
        }

        return $this->resultProduct;
    }

    public function hasResultProduct(): bool
    {
        return $this->resultProduct instanceof ProductInterface;
    }

    public function setResultProduct(?ProductInterface $resultProduct): void
    {
        $this->resultProduct = $resultProduct;
    }
}
