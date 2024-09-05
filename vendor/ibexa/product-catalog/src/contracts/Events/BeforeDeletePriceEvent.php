<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Events;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\ProductCatalog\Values\Price\Delete\Struct\ProductPriceDeleteStructInterface;

final class BeforeDeletePriceEvent extends BeforeEvent
{
    private ProductPriceDeleteStructInterface $deleteStruct;

    public function __construct(ProductPriceDeleteStructInterface $deleteStruct)
    {
        $this->deleteStruct = $deleteStruct;
    }

    public function getDeleteStruct(): ProductPriceDeleteStructInterface
    {
        return $this->deleteStruct;
    }
}
