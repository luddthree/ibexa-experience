<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\Region;

use Ibexa\Contracts\ProductCatalog\Values\RegionInterface;
use Ibexa\Core\MVC\Symfony\Event\ScopeChangeEvent;
use Ibexa\Core\MVC\Symfony\MVCEvents;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\ProductCatalog\Local\Repository\Region\RegionPoolFactoryInterface;
use Ibexa\ProductCatalog\Local\Repository\Region\RegionPoolInterface;
use Ibexa\ProductCatalog\Local\Repository\Region\RegionProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class RegionProviderTest extends TestCase
{
    private const EXAMPLE_REGION_IDENTIFIER = 'dach';

    /** @var \Ibexa\ProductCatalog\Local\Repository\Region\RegionPoolFactoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private RegionPoolFactoryInterface $factory;

    private RegionProvider $provider;

    protected function setUp(): void
    {
        $this->factory = $this->createMock(RegionPoolFactoryInterface::class);
        $this->provider = new RegionProvider($this->factory);
    }

    public function testGetRegionInitializePool(): void
    {
        $expectedRegion = $this->createMock(RegionInterface::class);

        $this->factory
            ->expects(self::once())
            ->method('createPool')
            ->willReturn($this->createPoolWithSingleRegion($expectedRegion));

        $actualRegion = $this->provider->getRegion(self::EXAMPLE_REGION_IDENTIFIER);
        self::assertSame($expectedRegion, $actualRegion);

        // Region pool shouldn't be reinitialized
        $actualRegion = $this->provider->getRegion(self::EXAMPLE_REGION_IDENTIFIER);
        self::assertSame($expectedRegion, $actualRegion);
    }

    public function testGetRegionsInitializePool(): void
    {
        $expectedRegions = [
            'foo' => $this->createMock(RegionInterface::class),
            'bar' => $this->createMock(RegionInterface::class),
            'baz' => $this->createMock(RegionInterface::class),
        ];

        $this->factory
            ->expects(self::once())
            ->method('createPool')
            ->willReturn($this->createPoolWithRegionList($expectedRegions));

        self::assertSame($expectedRegions, $this->provider->getRegions());
        // Region pool shouldn't be reinitialized
        self::assertSame($expectedRegions, $this->provider->getRegions());
    }

    public function testScopeChangeResetsRegionPool(): void
    {
        $expectedRegion = $this->createMock(RegionInterface::class);

        $this->factory
            ->expects(self::exactly(2))
            ->method('createPool')
            ->willReturn($this->createPoolWithSingleRegion($expectedRegion));

        // Initialize region pool
        $actualRegion = $this->provider->getRegion(self::EXAMPLE_REGION_IDENTIFIER);
        self::assertSame($expectedRegion, $actualRegion);

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber($this->provider);
        $eventDispatcher->dispatch(
            new ScopeChangeEvent($this->createMock(SiteAccess::class)),
            MVCEvents::CONFIG_SCOPE_CHANGE
        );

        // Region pool should be reinitialized
        $actualRegion = $this->provider->getRegion(self::EXAMPLE_REGION_IDENTIFIER);
        self::assertSame($expectedRegion, $actualRegion);
    }

    private function createPoolWithSingleRegion(RegionInterface $region): RegionPoolInterface
    {
        $pool = $this->createMock(RegionPoolInterface::class);
        $pool->method('getRegion')->with(self::EXAMPLE_REGION_IDENTIFIER)->willReturn($region);

        return $pool;
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\RegionInterface[] $regionList
     */
    private function createPoolWithRegionList(array $regionList): RegionPoolInterface
    {
        $pool = $this->createMock(RegionPoolInterface::class);
        $pool->method('getRegions')->willReturn($regionList);

        return $pool;
    }
}
