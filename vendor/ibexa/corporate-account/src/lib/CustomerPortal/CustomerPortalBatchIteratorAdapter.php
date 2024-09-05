<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\CustomerPortal;

use ArrayIterator;
use Ibexa\Contracts\Core\Repository\Iterator\BatchIteratorAdapter;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Iterator;

final class CustomerPortalBatchIteratorAdapter implements BatchIteratorAdapter
{
    private LocationService $locationService;

    private Location $customerPortalLocation;

    public function __construct(LocationService $locationService, Location $customerPortalLocation)
    {
        $this->locationService = $locationService;
        $this->customerPortalLocation = $customerPortalLocation;
    }

    public function fetch(int $offset, int $limit): Iterator
    {
        return new ArrayIterator(
            array_filter(
                $this->locationService->loadLocationChildren(
                    $this->customerPortalLocation,
                    $offset,
                    $limit
                )->locations,
                static fn (Location $location): bool => $location->getContent()->getContentType()->identifier === 'customer_portal'
            )
        );
    }
}
