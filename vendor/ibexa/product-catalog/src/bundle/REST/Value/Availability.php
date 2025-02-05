<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityInterface;
use Ibexa\Rest\Value;

final class Availability extends Value
{
    public AvailabilityInterface $availability;

    public function __construct(AvailabilityInterface $availability)
    {
        $this->availability = $availability;
    }
}
