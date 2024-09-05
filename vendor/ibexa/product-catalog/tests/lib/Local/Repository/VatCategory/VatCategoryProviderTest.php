<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\VatCategory;

use Ibexa\Contracts\ProductCatalog\Values\VatCategoryInterface;
use Ibexa\Core\MVC\Symfony\Event\ScopeChangeEvent;
use Ibexa\Core\MVC\Symfony\MVCEvents;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\ProductCatalog\Local\Repository\VatCategory\VatCategoryPoolFactoryInterface;
use Ibexa\ProductCatalog\Local\Repository\VatCategory\VatCategoryPoolInterface;
use Ibexa\ProductCatalog\Local\Repository\VatCategory\VatCategoryProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class VatCategoryProviderTest extends TestCase
{
    /** @var \Ibexa\ProductCatalog\Local\Repository\VatCategory\VatCategoryPoolFactoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private VatCategoryPoolFactoryInterface $factory;

    private VatCategoryProvider $provider;

    protected function setUp(): void
    {
        $this->factory = $this->createMock(VatCategoryPoolFactoryInterface::class);
        $this->provider = new VatCategoryProvider($this->factory);
    }

    public function testGetVatCategoryInitializePool(): void
    {
        $expectedVatCategory = $this->createMock(VatCategoryInterface::class);

        $this->factory
            ->expects(self::once())
            ->method('createPool')
            ->willReturn($this->createPoolWithSingleVatCategory($expectedVatCategory));

        $actualVatCategory = $this->provider->getVatCategory('foo', 'standard');
        self::assertSame($expectedVatCategory, $actualVatCategory);

        // Vat categories pool shouldn't be reinitialized
        $actualVatCategory = $this->provider->getVatCategory('foo', 'standard');
        self::assertSame($expectedVatCategory, $actualVatCategory);
    }

    public function testGetVatCategoriesInitializePool(): void
    {
        $expectedVatCategories = [
            'standard' => $this->createMock(VatCategoryInterface::class),
            'reduced' => $this->createMock(VatCategoryInterface::class),
        ];

        $this->factory
            ->expects(self::once())
            ->method('createPool')
            ->willReturn($this->createPoolWithVatCategoriesList($expectedVatCategories));

        self::assertSame($expectedVatCategories, $this->provider->getVatCategories('foo'));
        // Vat categories pool shouldn't be reinitialized
        self::assertSame($expectedVatCategories, $this->provider->getVatCategories('foo'));
    }

    public function testScopeChangeResetsVatCategoriesPool(): void
    {
        $expectedVatCategory = $this->createMock(VatCategoryInterface::class);

        $this->factory
            ->expects(self::exactly(2))
            ->method('createPool')
            ->willReturn($this->createPoolWithSingleVatCategory($expectedVatCategory));

        // Initialize vat categories pool
        $actualVatCategory = $this->provider->getVatCategory('foo', 'standard');
        self::assertSame($expectedVatCategory, $actualVatCategory);

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber($this->provider);
        $eventDispatcher->dispatch(
            new ScopeChangeEvent($this->createMock(SiteAccess::class)),
            MVCEvents::CONFIG_SCOPE_CHANGE
        );

        // Vat categories pool should be reinitialized
        $actualVatCategory = $this->provider->getVatCategory('foo', 'standard');
        self::assertSame($expectedVatCategory, $actualVatCategory);
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\VatCategoryInterface[] $expectedVatCategories
     */
    private function createPoolWithVatCategoriesList(
        array $expectedVatCategories
    ): VatCategoryPoolInterface {
        $pool = $this->createMock(VatCategoryPoolInterface::class);
        $pool->method('getVatCategories')->willReturn($expectedVatCategories);

        return $pool;
    }

    private function createPoolWithSingleVatCategory(
        VatCategoryInterface $expectedVatCategory
    ): VatCategoryPoolInterface {
        $pool = $this->createMock(VatCategoryPoolInterface::class);
        $pool->method('getVatCategory')->willReturn($expectedVatCategory);

        return $pool;
    }
}
