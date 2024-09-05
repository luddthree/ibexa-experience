<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Events;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\ProductCatalog\Values\Price\Update\Struct\ProductPriceUpdateStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use UnexpectedValueException;

final class BeforeUpdatePriceEvent extends BeforeEvent
{
    private ProductPriceUpdateStructInterface $updateStruct;

    private ?PriceInterface $resultPrice = null;

    public function __construct(ProductPriceUpdateStructInterface $updateStruct)
    {
        $this->updateStruct = $updateStruct;
    }

    public function getUpdateStruct(): ProductPriceUpdateStructInterface
    {
        return $this->updateStruct;
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
