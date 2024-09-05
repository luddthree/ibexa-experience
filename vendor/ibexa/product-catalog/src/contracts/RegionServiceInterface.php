<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog;

use Ibexa\Contracts\ProductCatalog\Values\Region\RegionListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Region\RegionQuery;
use Ibexa\Contracts\ProductCatalog\Values\RegionInterface;

interface RegionServiceInterface
{
    public function findRegions(?RegionQuery $query = null): RegionListInterface;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function getRegion(string $identifier): RegionInterface;
}
