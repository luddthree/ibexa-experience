<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Region;

use Ibexa\Contracts\ProductCatalog\Values\RegionInterface;

interface RegionPoolInterface
{
    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function getRegion(string $identifier): RegionInterface;

    /**
     * @return iterable<array-key,\Ibexa\Contracts\ProductCatalog\Values\RegionInterface>
     */
    public function getRegions(): iterable;
}
