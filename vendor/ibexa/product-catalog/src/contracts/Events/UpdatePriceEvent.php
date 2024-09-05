<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Events;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\ProductCatalog\Values\Price\Update\Struct\ProductPriceUpdateStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;

final class UpdatePriceEvent extends AfterEvent
{
    private ProductPriceUpdateStructInterface $updateStruct;

    private PriceInterface $price;

    public function __construct(PriceInterface $price, ProductPriceUpdateStructInterface $updateStruct)
    {
        $this->updateStruct = $updateStruct;
        $this->price = $price;
    }

    public function getUpdateStruct(): ProductPriceUpdateStructInterface
    {
        return $this->updateStruct;
    }

    public function getPrice(): PriceInterface
    {
        return $this->price;
    }
}
