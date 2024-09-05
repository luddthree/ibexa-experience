<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Dashboard\Iterator\BatchIteratorAdapter;

use ArrayIterator;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationList;
use Ibexa\Contracts\Dashboard\Iterator\BatchIteratorAdapter\DashboardFetchAdapter;
use PHPUnit\Framework\TestCase;

final class DashboardFetchAdapterTest extends TestCase
{
    private const EXAMPLE_OFFSET = 10;
    private const EXAMPLE_LIMIT = 100;

    public function testFetch(): void
    {
        $locations = [
            $this->createMock(Location::class),
            $this->createMock(Location::class),
            $this->createMock(Location::class),
        ];

        $expectedLocationList = $this->createLocationList($locations);
        $parentLocation = $this->createMock(Location::class);
        $locationService = $this->createMock(LocationService::class);
        $locationService
            ->method('loadLocationChildren')
            ->with($parentLocation, self::EXAMPLE_OFFSET, self::EXAMPLE_LIMIT)
            ->willReturn($expectedLocationList);

        $adapter = new DashboardFetchAdapter($locationService, $parentLocation);

        self::assertEquals(
            new ArrayIterator($locations),
            $adapter->fetch(self::EXAMPLE_OFFSET, self::EXAMPLE_LIMIT)
        );
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location[] $locations
     */
    private function createLocationList(array $locations): LocationList
    {
        $list = $this->createMock(LocationList::class);
        $list->method('getIterator')->willReturn(new ArrayIterator($locations));

        return $list;
    }
}
