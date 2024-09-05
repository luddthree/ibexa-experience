<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Bundle\ProductCatalog\EventSubscriber\RegionResolverSubscriber;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\ProductCatalog\Events\RegionResolveEvent;
use Ibexa\Contracts\ProductCatalog\RegionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\RegionInterface;
use PHPUnit\Framework\TestCase;

final class RegionResolverSubscriberTest extends TestCase
{
    /** @var \Ibexa\Contracts\ProductCatalog\RegionServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private RegionServiceInterface $regionService;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ConfigResolverInterface $configResolver;

    protected function setUp(): void
    {
        $this->regionService = $this->createMock(RegionServiceInterface::class);
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
    }

    public function testOnRegionResolve(): void
    {
        $expectedRegion = $this->createMock(RegionInterface::class);

        $this->regionService
            ->method('getRegion')
            ->with('dach')
            ->willReturn($expectedRegion);

        $this->configResolver
            ->method('getParameter')
            ->with('product_catalog.regions')
            ->willReturn(['dach', 'france', 'row']);

        $event = new RegionResolveEvent();

        $subscriber = new RegionResolverSubscriber($this->regionService, $this->configResolver);
        $subscriber->onRegionResolve($event);

        self::assertSame(
            $expectedRegion,
            $event->getRegion()
        );
    }

    public function testOnRegionResolveWithMissingRegionsConfiguration(): void
    {
        $this->configResolver
            ->method('getParameter')
            ->with('product_catalog.regions')
            ->willReturn([/* Empty */]);

        $event = new RegionResolveEvent();

        $subscriber = new RegionResolverSubscriber($this->regionService, $this->configResolver);
        $subscriber->onRegionResolve($event);

        self::assertNull($event->getRegion());
    }
}
