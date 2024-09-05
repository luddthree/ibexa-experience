<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository;

use Ibexa\Contracts\ProductCatalog\Events\RegionResolveEvent;
use Ibexa\Contracts\ProductCatalog\Exceptions\ConfigurationException;
use Ibexa\Contracts\ProductCatalog\Values\RegionInterface;
use Ibexa\ProductCatalog\Local\Repository\RegionResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class RegionResolverTest extends TestCase
{
    public function testResolveRegion(): void
    {
        $expectedRegion = $this->createMock(RegionInterface::class);

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects(self::once())
            ->method('dispatch')
            ->with(self::isInstanceOf(RegionResolveEvent::class))
            ->willReturn(new RegionResolveEvent($expectedRegion));

        $resolver = new RegionResolver($eventDispatcher);

        self::assertSame($expectedRegion, $resolver->resolveRegion());
    }

    public function testResolveRegionThrowsConfigurationException(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Unable to resolve region');

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects(self::once())
            ->method('dispatch')
            ->with(self::isInstanceOf(RegionResolveEvent::class))
            ->willReturnArgument(0);

        $resolver = new RegionResolver($eventDispatcher);
        $resolver->resolveRegion();
    }
}
