<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values;

use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityContextInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityInterface;

interface AvailabilityAwareInterface extends ProductInterface
{
    public function getAvailability(?AvailabilityContextInterface $context = null): AvailabilityInterface;

    /**
     * Return true if product availability is defined and product is available.
     */
    public function isAvailable(?AvailabilityContextInterface $context = null): bool;

    public function hasAvailability(): bool;
}
