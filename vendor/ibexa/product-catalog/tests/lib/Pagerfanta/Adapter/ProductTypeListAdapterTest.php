<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Pagerfanta\Adapter;

use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductType\ProductTypeQuery;
use Ibexa\ProductCatalog\Local\Repository\Values\ProductTypeList;
use Ibexa\ProductCatalog\Pagerfanta\Adapter\ProductTypeListAdapter;
use PHPUnit\Framework\TestCase;

final class ProductTypeListAdapterTest extends TestCase
{
    private const EXAMPLE_OFFSET = 10;
    private const EXAMPLE_LIMIT = 100;
    private const EXAMPLE_TOTAL_COUNT = 100;

    /** @var \Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ProductTypeServiceInterface $productTypeService;

    private ProductTypeListAdapter $adapter;

    protected function setUp(): void
    {
        $this->productTypeService = $this->createMock(ProductTypeServiceInterface::class);

        $this->adapter = new ProductTypeListAdapter(
            $this->productTypeService,
            new ProductTypeQuery('prefix')
        );
    }

    public function testGetSlice(): void
    {
        $expectedList = new ProductTypeList();

        $this->productTypeService
            ->method('findProductTypes')
            ->with(new ProductTypeQuery('prefix', self::EXAMPLE_OFFSET, self::EXAMPLE_LIMIT))
            ->willReturn($expectedList);

        self::assertSame(
            $expectedList,
            $this->adapter->getSlice(self::EXAMPLE_OFFSET, self::EXAMPLE_LIMIT)
        );
    }

    public function testGetNbResults(): void
    {
        $this->productTypeService
            ->method('findProductTypes')
            ->with(new ProductTypeQuery('prefix', 0, 0))
            ->willReturn(new ProductTypeList(
                [/* Not important for this test case */],
                self::EXAMPLE_TOTAL_COUNT
            ));

        self::assertEquals(
            self::EXAMPLE_TOTAL_COUNT,
            $this->adapter->getNbResults()
        );
    }
}
