<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\Region;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\Values\RegionInterface;
use Ibexa\ProductCatalog\Local\Repository\Region\RegionPool;
use PHPUnit\Framework\TestCase;

final class RegionPoolTest extends TestCase
{
    public function testGetRegion(): void
    {
        $foo = $this->createMock(RegionInterface::class);
        $bar = $this->createMock(RegionInterface::class);
        $baz = $this->createMock(RegionInterface::class);

        $pool = new RegionPool([
            'foo' => $foo,
            'bar' => $bar,
            'baz' => $baz,
        ]);

        self::assertSame($foo, $pool->getRegion('foo'));
        self::assertSame($bar, $pool->getRegion('bar'));
        self::assertSame($baz, $pool->getRegion('baz'));
    }

    public function testGetRegionThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Could not find 'Ibexa\Contracts\ProductCatalog\Values\RegionInterface' with identifier 'non-existing'");

        $pool = new RegionPool([
            'foo' => $this->createMock(RegionInterface::class),
            'bar' => $this->createMock(RegionInterface::class),
            'baz' => $this->createMock(RegionInterface::class),
        ]);
        $pool->getRegion('non-existing');
    }

    public function testGetRegions(): void
    {
        $regions = [
            'foo' => $this->createMock(RegionInterface::class),
            'bar' => $this->createMock(RegionInterface::class),
            'baz' => $this->createMock(RegionInterface::class),
        ];

        $pool = new RegionPool($regions);

        self::assertEquals($regions, $pool->getRegions());
    }
}
