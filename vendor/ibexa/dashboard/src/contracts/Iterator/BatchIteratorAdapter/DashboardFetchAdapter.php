<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Dashboard\Iterator\BatchIteratorAdapter;

use Ibexa\Contracts\Core\Repository\Iterator\BatchIteratorAdapter;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Iterator;

final class DashboardFetchAdapter implements BatchIteratorAdapter
{
    private LocationService $locationService;

    private Location $location;

    public function __construct(
        LocationService $locationService,
        Location $location
    ) {
        $this->locationService = $locationService;
        $this->location = $location;
    }

    /**
     * @return \Iterator<mixed,\Ibexa\Contracts\Core\Repository\Values\Content\Location>
     */
    public function fetch(int $offset, int $limit): Iterator
    {
        /** @var \ArrayIterator<int,\Ibexa\Contracts\Core\Repository\Values\Content\Location> */
        return $this->locationService->loadLocationChildren($this->location, $offset, $limit)->getIterator();
    }
}
