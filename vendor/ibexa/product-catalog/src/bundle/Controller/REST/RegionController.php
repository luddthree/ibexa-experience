<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\REST;

use Ibexa\Bundle\ProductCatalog\REST\Value\Region;
use Ibexa\Bundle\ProductCatalog\REST\Value\RegionList;
use Ibexa\Contracts\ProductCatalog\RegionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Region\RegionQuery;
use Ibexa\Rest\Server\Controller as RestController;
use Ibexa\Rest\Value;

final class RegionController extends RestController
{
    private RegionServiceInterface $regionService;

    public function __construct(RegionServiceInterface $regionService)
    {
        $this->regionService = $regionService;
    }

    public function listRegionsAction(): Value
    {
        $restRegions = [];
        $query = new RegionQuery(null, [], null);
        $regions = $this->regionService->findRegions($query);

        foreach ($regions as $region) {
            $restRegions[] = new Region($region);
        }

        return new RegionList($restRegions);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function getRegionAction(string $identifier): Value
    {
        $region = $this->regionService->getRegion($identifier);

        return new Region($region);
    }
}
