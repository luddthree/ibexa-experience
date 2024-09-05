<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Events;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;

final class BeforeExecutePriceStructsEvent extends BeforeEvent
{
    /** @var iterable<\Ibexa\Contracts\ProductCatalog\Values\Price\ProductPriceStructInterface> */
    private iterable $priceStructs;

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Values\Price\ProductPriceStructInterface> $structs
     */
    public function __construct(iterable $structs)
    {
        $this->priceStructs = $structs;
    }

    /**
     * @return iterable<\Ibexa\Contracts\ProductCatalog\Values\Price\ProductPriceStructInterface>
     */
    public function getPriceStructs(): iterable
    {
        return $this->priceStructs;
    }
}
