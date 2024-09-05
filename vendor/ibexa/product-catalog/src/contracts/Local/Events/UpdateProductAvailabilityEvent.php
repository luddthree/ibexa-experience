<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Events;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityUpdateStruct;

final class UpdateProductAvailabilityEvent extends AfterEvent
{
    private AvailabilityInterface $productAvailability;

    private ProductAvailabilityUpdateStruct $updateStruct;

    public function __construct(
        AvailabilityInterface $productAvailability,
        ProductAvailabilityUpdateStruct $updateStruct
    ) {
        $this->productAvailability = $productAvailability;
        $this->updateStruct = $updateStruct;
    }

    public function getProductAvailability(): AvailabilityInterface
    {
        return $this->productAvailability;
    }

    public function getUpdateStruct(): ProductAvailabilityUpdateStruct
    {
        return $this->updateStruct;
    }
}
