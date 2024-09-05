<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Pagerfanta\Adapter;

use Ibexa\Contracts\ProductCatalog\CatalogServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogQuery;
use Ibexa\ProductCatalog\Local\Repository\Values\Catalog\CatalogList;
use Ibexa\ProductCatalog\Pagerfanta\Adapter\CatalogListAdapter;
use PHPUnit\Framework\TestCase;

final class CatalogListAdapterTest extends TestCase
{
    private const EXAMPLE_OFFSET = 10;
    private const EXAMPLE_LIMIT = 100;
    private const EXAMPLE_TOTAL_COUNT = 100;

    /** @var \Ibexa\Contracts\ProductCatalog\CatalogServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private CatalogServiceInterface $catalogService;

    private CatalogListAdapter $adapter;

    protected function setUp(): void
    {
        $this->catalogService = $this->createMock(CatalogServiceInterface::class);
        $this->adapter = new CatalogListAdapter(
            $this->catalogService,
            new CatalogQuery()
        );
    }

    public function testGetSlice(): void
    {
        $expectedList = new CatalogList();

        $this->catalogService
            ->method('findCatalogs')
            ->with(new CatalogQuery(null, [], self::EXAMPLE_LIMIT, self::EXAMPLE_OFFSET))
            ->willReturn($expectedList);

        self::assertSame(
            $expectedList,
            $this->adapter->getSlice(self::EXAMPLE_OFFSET, self::EXAMPLE_LIMIT)
        );
    }

    public function testGetNbResults(): void
    {
        $this->catalogService
            ->method('findCatalogs')
            ->with(new CatalogQuery(null, [], 0, 0))
            ->willReturn(new CatalogList(
                [/* Not important for this test case */],
                self::EXAMPLE_TOTAL_COUNT
            ));

        self::assertEquals(
            self::EXAMPLE_TOTAL_COUNT,
            $this->adapter->getNbResults()
        );
    }
}
