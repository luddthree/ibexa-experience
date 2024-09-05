<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Events;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\ProductPriceCreateStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;

final class CreatePriceEvent extends AfterEvent
{
    private ProductPriceCreateStructInterface $createStruct;

    private PriceInterface $price;

    public function __construct(ProductPriceCreateStructInterface $createStruct, PriceInterface $price)
    {
        $this->createStruct = $createStruct;
        $this->price = $price;
    }

    public function getCreateStruct(): ProductPriceCreateStructInterface
    {
        return $this->createStruct;
    }

    public function getPrice(): PriceInterface
    {
        return $this->price;
    }
}
