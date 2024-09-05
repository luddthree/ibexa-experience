<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Iterator\BatchIteratorAdapter;

use ArrayIterator;
use Ibexa\Contracts\ProductCatalog\Iterator\BatchIteratorAdapter\ProductFetchAdapter;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use PHPUnit\Framework\TestCase;

final class ProductFetchAdapterTest extends TestCase
{
    private const EXAMPLE_OFFSET = 10;
    private const EXAMPLE_LIMIT = 25;

    public function testFetch(): void
    {
        $criterion = $this->createMock(CriterionInterface::class);
        $sortClauses = [
            $this->createMock(SortClause::class),
        ];

        $expectedResults = [
            $this->createMock(ProductInterface::class),
            $this->createMock(ProductInterface::class),
            $this->createMock(ProductInterface::class),
        ];

        $productService = $this->createMock(ProductServiceInterface::class);
        $productService
            ->method('findProducts')
            ->with(new ProductQuery(
                null,
                $criterion,
                $sortClauses,
                self::EXAMPLE_OFFSET,
                self::EXAMPLE_LIMIT,
            ))
            ->willReturn($this->createProductsList($expectedResults));

        $originalQuery = new ProductQuery(null, $criterion, $sortClauses, 0, 0);
        $adapter = new ProductFetchAdapter($productService, $originalQuery);

        self::assertSame(
            $expectedResults,
            iterator_to_array($adapter->fetch(self::EXAMPLE_OFFSET, self::EXAMPLE_LIMIT))
        );

        // Input $query remains untouched
        self::assertSame(0, $originalQuery->getOffset());
        self::assertSame(0, $originalQuery->getLimit());
    }

    /**
     * @param array<\Ibexa\Contracts\ProductCatalog\Values\ProductInterface> $products
     */
    private function createProductsList(array $products): ProductListInterface
    {
        $list = $this->createMock(ProductListInterface::class);
        $list->method('getIterator')->willReturn(new ArrayIterator($products));

        return $list;
    }
}
