<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Personalization\Storage;

use Ibexa\Personalization\Criteria\Criteria;
use Ibexa\Personalization\Exception\ItemNotFoundException;
use Ibexa\Personalization\Value\Storage\ItemList;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use Ibexa\ProductCatalog\Personalization\Storage\ProductDataSource;
use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;

/**
 * @covers \Ibexa\ProductCatalog\Personalization\Storage\ProductDataSource
 */
final class ProductDataSourceTest extends IbexaKernelTestCase
{
    private const PRODUCT_ID_1 = '71';
    private const PRODUCT_ID_2 = '72';
    private const PRODUCT_TYPE_ID = 'jeans';
    private const LANGUAGE_EN = 'eng-GB';

    protected function setUp(): void
    {
        self::bootKernel();
        self::getLanguageResolver()->setContextLanguage(self::LANGUAGE_EN);
        self::setAnonymousUser();
    }

    public function testCountItems(): void
    {
        $criteria = new Criteria(
            [self::PRODUCT_TYPE_ID],
            [self::LANGUAGE_EN],
            0,
        );

        $count = self::getProductDataSource()->countItems(
            $criteria
        );

        self::assertSame(2, $count);
    }

    public function testFetchItems(): void
    {
        $criteria = new Criteria(
            [self::PRODUCT_TYPE_ID],
            [self::LANGUAGE_EN],
        );

        $items = self::getProductDataSource()->fetchItems(
            $criteria
        );

        self::assertCount(2, $items);
        self::assertInstanceOf(ItemList::class, $items);
        self::assertTrue($items->has(self::PRODUCT_ID_1, self::LANGUAGE_EN));
        self::assertTrue($items->has(self::PRODUCT_ID_2, self::LANGUAGE_EN));
    }

    public function testFetchItem(): void
    {
        $item = self::getProductDataSource()->fetchItem(
            self::PRODUCT_ID_1,
            self::LANGUAGE_EN
        );

        self::assertSame(self::PRODUCT_ID_1, $item->getId());
        self::assertSame(self::LANGUAGE_EN, $item->getLanguage());
        self::assertSame(self::PRODUCT_TYPE_ID, $item->getType()->getIdentifier());
    }

    public function testCountItemsForNonLocalEngine(): void
    {
        self::getContainer()->set(ConfigProviderInterface::class, $this->getConfigProviderMock());

        self::assertSame(
            0,
            self::getProductDataSource()->countItems(
                new Criteria(
                    [self::PRODUCT_TYPE_ID],
                    [self::LANGUAGE_EN],
                )
            )
        );
    }

    public function testFetchItemsForNonLocalEngine(): void
    {
        self::getContainer()->set(ConfigProviderInterface::class, $this->getConfigProviderMock());

        self::assertEquals(
            new ItemList([]),
            self::getProductDataSource()->fetchItems(
                new Criteria(
                    [self::PRODUCT_TYPE_ID],
                    [self::LANGUAGE_EN],
                )
            )
        );
    }

    public function testFetchItemThrowItemNotFoundException(): void
    {
        self::getContainer()->set(ConfigProviderInterface::class, $this->getConfigProviderMock());

        $this->expectException(ItemNotFoundException::class);
        $this->expectExceptionMessage('Item not found with id: 71 and language: eng-GB');

        self::getProductDataSource()->fetchItem(
            self::PRODUCT_ID_1,
            self::LANGUAGE_EN
        );
    }

    private static function getProductDataSource(): ProductDataSource
    {
        return self::getServiceByClassName(ProductDataSource::class);
    }

    private function getConfigProviderMock(): ConfigProviderInterface
    {
        $configProviderMock = $this->createMock(ConfigProviderInterface::class);
        $configProviderMock->method('getEngineType')->willReturn('foo');

        return $configProviderMock;
    }
}
