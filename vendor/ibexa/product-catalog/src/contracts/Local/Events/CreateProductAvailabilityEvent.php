<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Events;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityCreateStruct;

final class CreateProductAvailabilityEvent extends AfterEvent
{
    private ProductAvailabilityCreateStruct $createStruct;

    private AvailabilityInterface $productAvailability;

    public function __construct(
        ProductAvailabilityCreateStruct $createStruct,
        AvailabilityInterface $productAvailability
    ) {
        $this->createStruct = $createStruct;
        $this->productAvailability = $productAvailability;
    }

    public function getCreateStruct(): ProductAvailabilityCreateStruct
    {
        return $this->createStruct;
    }

    public function getProductAvailability(): AvailabilityInterface
    {
        return $this->productAvailability;
    }
}
