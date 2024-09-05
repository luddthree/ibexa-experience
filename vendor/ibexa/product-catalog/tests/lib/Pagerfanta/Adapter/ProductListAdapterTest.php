<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Pagerfanta\Adapter;

use ArrayIterator;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\ProductCatalog\Pagerfanta\Adapter\ProductListAdapter;
use PHPUnit\Framework\MockObject\Rule\InvokedCount;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\ProductCatalog\Pagerfanta\Adapter\ProductListAdapter
 */
final class ProductListAdapterTest extends TestCase
{
    public function testGetSliceCallsProductService(): void
    {
        $productList = $this->createProductListMock(self::once(), self::never());
        $productService = $this->createMockProductService($productList);

        $adapter = new ProductListAdapter($productService, new ProductQuery());
        $iterator = $adapter->getSlice(0, PHP_INT_MAX);
        iterator_to_array($iterator);
    }

    public function testGetNbResultsCallsProductService(): void
    {
        $productList = $this->createProductListMock(self::never(), self::once());
        $productService = $this->createMockProductService($productList);

        $adapter = new ProductListAdapter($productService, new ProductQuery());

        $adapter->getNbResults();
    }

    private function createMockProductService(ProductListInterface $productList): ProductServiceInterface
    {
        $productService = $this->createMock(ProductServiceInterface::class);

        $productService
            ->method('findProducts')
            ->willReturn($productList);

        return $productService;
    }

    private function createProductListMock(
        InvokedCount $iteratorInvocationCount,
        InvokedCount $countInvocationCount
    ): ProductListInterface {
        $productList = $this->createMock(ProductListInterface::class);
        $productList->expects($countInvocationCount)
            ->method('getTotalCount');

        $productList->expects($iteratorInvocationCount)
            ->method('getIterator')
            ->willReturn(new ArrayIterator());

        return $productList;
    }
}
