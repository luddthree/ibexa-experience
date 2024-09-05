<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository;

use Ibexa\Contracts\ProductCatalog\Events\CurrencyResolveEvent;
use Ibexa\Contracts\ProductCatalog\Exceptions\ConfigurationException;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\ProductCatalog\Local\Repository\CurrencyResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class CurrencyResolverTest extends TestCase
{
    public function testResolveCurrency(): void
    {
        $expectedCurrency = $this->createMock(CurrencyInterface::class);

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects(self::once())
            ->method('dispatch')
            ->with(self::isInstanceOf(CurrencyResolveEvent::class))
            ->willReturn(new CurrencyResolveEvent($expectedCurrency));

        $resolver = new CurrencyResolver($eventDispatcher);

        self::assertSame($expectedCurrency, $resolver->resolveCurrency());
    }

    public function testResolveCurrencyThrowsConfigurationException(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage(
            'Cannot resolve currency. Missing configuration under the ibexa.system.[your_siteaccess].product_catalog.currencies key'
        );

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects(self::once())
            ->method('dispatch')
            ->with(self::isInstanceOf(CurrencyResolveEvent::class))
            ->willReturnArgument(0);

        $resolver = new CurrencyResolver($eventDispatcher);
        $resolver->resolveCurrency();
    }
}
