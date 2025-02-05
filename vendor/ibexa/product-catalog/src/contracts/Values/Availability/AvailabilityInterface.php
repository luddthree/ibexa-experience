<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Availability;

use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

interface AvailabilityInterface
{
    public function isAvailable(): bool;

    public function getStock(): ?int;

    public function isInfinite(): bool;

    public function getProduct(): ProductInterface;
}
