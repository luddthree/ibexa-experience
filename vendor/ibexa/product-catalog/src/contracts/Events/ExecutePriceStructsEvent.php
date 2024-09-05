<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Events;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;

final class ExecutePriceStructsEvent extends AfterEvent
{
    /** @var iterable<\Ibexa\Contracts\ProductCatalog\Values\Price\ProductPriceStructInterface> */
    private iterable $priceStructs;

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Values\Price\ProductPriceStructInterface> $priceStructs
     */
    public function __construct(iterable $priceStructs)
    {
        $this->priceStructs = $priceStructs;
    }

    /**
     * @return iterable<\Ibexa\Contracts\ProductCatalog\Values\Price\ProductPriceStructInterface>
     */
    public function getPriceStructs(): iterable
    {
        return $this->priceStructs;
    }
}
