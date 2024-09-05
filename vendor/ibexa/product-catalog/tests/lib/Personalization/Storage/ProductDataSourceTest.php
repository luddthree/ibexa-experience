<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Personalization\Storage;

use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Personalization\Exception\ItemNotFoundException;
use Ibexa\Personalization\Value\Storage\ItemList;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use Ibexa\ProductCatalog\Personalization\Storage\ProductDataSource;
use Ibexa\Tests\ProductCatalog\Personalization\Creator\DataSourceTestItemFactory;

/**
 * @covers \Ibexa\ProductCatalog\Personalization\Storage\ProductDataSource
 */
final class ProductDataSourceTest extends AbstractProductDataSourceTestCase
{
    private const ERROR_MESSAGE = 'Only Local Product Catalog could be used as a data source';

    public function testCountItemsLogError(): void
    {
        $this->logger
            ->method('error')
            ->with(self::ERROR_MESSAGE);

        $this->configProvider
            ->method('getEngineType')
            ->willReturn('foo');

        $criteria = $this->itemCreator->createTestCriteria(
            [DataSourceTestItemFactory::PRODUCT_TYPE_IDENTIFIER],
            [DataSourceTestItemFactory::LANGUAGE_EN, DataSourceTestItemFactory::LANGUAGE_DE]
        );

        $productDataSource = new ProductDataSource(
            $this->dataResolver,
            $this->logger,
            $this->productService,
            $this->configProvider,
            $this->contentService,
            $this->createRepositoryMock()
        );

        self::assertSame(0, $productDataSource->countItems($criteria));
    }

    public function testFetchItemsLogError(): void
    {
        $this->logger
            ->method('error')
            ->with(self::ERROR_MESSAGE);
        $configProvider = $this->createMock(ConfigProviderInterface::class);
        $configProvider
            ->method('getEngineType')
            ->willReturn('foo');

        $criteria = $this->itemCreator->createTestCriteria(
            [DataSourceTestItemFactory::PRODUCT_TYPE_IDENTIFIER],
            [DataSourceTestItemFactory::LANGUAGE_EN, DataSourceTestItemFactory::LANGUAGE_DE]
        );

        $productDataSource = new ProductDataSource(
            $this->dataResolver,
            $this->logger,
            $this->productService,
            $configProvider,
            $this->contentService,
            $this->createRepositoryMock()
        );

        self::assertEquals(new ItemList([]), $productDataSource->fetchItems($criteria));
    }

    public function testFetchItemNoLocalEngineThrowItemNotFoundException(): void
    {
        $itemId = '10';
        $language = 'pl';

        $configProvider = $this->createMock(ConfigProviderInterface::class);
        $configProvider
            ->method('getEngineType')
            ->willReturn('foo');

        $productDataSource = new ProductDataSource(
            $this->dataResolver,
            $this->logger,
            $this->productService,
            $configProvider,
            $this->contentService,
            $this->repository
        );

        $this->expectException(ItemNotFoundException::class);
        $this->expectExceptionMessage(sprintf('Item not found with id: %s and language: %s', $itemId, $language));

        $productDataSource->fetchItem($itemId, $language);
    }

    public function testFetchItemThrowItemNotFoundException(): void
    {
        $itemId = '10';
        $language = 'pl';

        $this->expectException(ItemNotFoundException::class);
        $this->expectExceptionMessage(sprintf('Item not found with id: %s and language: %s', $itemId, $language));

        $this->contentService
            ->expects(self::once())
            ->method('loadContent')
            ->with($itemId, [$language])
            ->willThrowException(new NotFoundException('content', $itemId));

        $this->productService
            ->method('getProductVariant')
            ->willThrowException(new NotFoundException('content', $itemId));

        $this->productDataSource->fetchItem($itemId, $language);
    }
}
