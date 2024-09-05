<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Events;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\ProductPriceCreateStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use UnexpectedValueException;

final class BeforeCreatePriceEvent extends BeforeEvent
{
    private ProductPriceCreateStructInterface $createStruct;

    private ?PriceInterface $resultPrice = null;

    public function __construct(ProductPriceCreateStructInterface $createStruct)
    {
        $this->createStruct = $createStruct;
    }

    public function getCreateStruct(): ProductPriceCreateStructInterface
    {
        return $this->createStruct;
    }

    public function getResultPrice(): PriceInterface
    {
        if ($this->resultPrice === null) {
            $message = 'Return value is not set or not of type %s. Check hasResultPrice() or'
                . ' set it using setResultPrice() before you call the getter.';

            throw new UnexpectedValueException(sprintf($message, PriceInterface::class));
        }

        return $this->resultPrice;
    }

    public function hasResultPrice(): bool
    {
        return $this->resultPrice instanceof PriceInterface;
    }

    public function setResultPrice(?PriceInterface $resultPrice): void
    {
        $this->resultPrice = $resultPrice;
    }
}
