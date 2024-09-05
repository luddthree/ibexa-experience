<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Bundle\ProductCatalog\EventSubscriber\CurrencyResolverSubscriber;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\Events\CurrencyResolveEvent;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use PHPUnit\Framework\TestCase;

final class CurrencyResolverSubscriberTest extends TestCase
{
    /** @var \Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private CurrencyServiceInterface $currencyService;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ConfigResolverInterface $configResolver;

    protected function setUp(): void
    {
        $this->currencyService = $this->createMock(CurrencyServiceInterface::class);
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
    }

    public function testOnCurrencyResolve(): void
    {
        $expectedCurrency = $this->createMock(CurrencyInterface::class);

        $this->currencyService
            ->method('getCurrencyByCode')
            ->with('EUR')
            ->willReturn($expectedCurrency);

        $this->configResolver
            ->method('getParameter')
            ->with('product_catalog.currencies')
            ->willReturn(['EUR', 'USD', 'PLN']);

        $event = new CurrencyResolveEvent();

        $subscriber = new CurrencyResolverSubscriber($this->currencyService, $this->configResolver);
        $subscriber->onCurrencyResolve($event);

        self::assertSame(
            $expectedCurrency,
            $event->getCurrency()
        );
    }

    public function testOnCurrencyResolveWithMissingCurrenciesConfiguration(): void
    {
        $this->configResolver
            ->method('getParameter')
            ->with('product_catalog.currencies')
            ->willReturn([/* Empty */]);

        $event = new CurrencyResolveEvent();

        $subscriber = new CurrencyResolverSubscriber($this->currencyService, $this->configResolver);
        $subscriber->onCurrencyResolve($event);

        self::assertNull($event->getCurrency());
    }
}
