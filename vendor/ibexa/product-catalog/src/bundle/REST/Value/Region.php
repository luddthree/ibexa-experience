<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\ProductCatalog\Values\RegionInterface;
use Ibexa\Rest\Value;

final class Region extends Value
{
    public RegionInterface $region;

    public function __construct(RegionInterface $region)
    {
        $this->region = $region;
    }
}
