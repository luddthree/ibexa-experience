<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Iterator\BatchIteratorAdapter;

use ArrayIterator;
use Ibexa\Contracts\ProductCatalog\Iterator\BatchIteratorAdapter\ProductTypeListAdapter;
use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductType\ProductTypeListInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductType\ProductTypeQuery;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use PHPUnit\Framework\TestCase;

final class ProductTypeListAdapterTest extends TestCase
{
    private const EXAMPLE_OFFSET = 10;
    private const EXAMPLE_LIMIT = 100;

    public function testFetch(): void
    {
        $types = [
            $this->createMock(ProductTypeInterface::class),
            $this->createMock(ProductTypeInterface::class),
            $this->createMock(ProductTypeInterface::class),
        ];

        $expectedProductTypeList = $this->createProductTypesList($types);

        $productTypeService = $this->createMock(ProductTypeServiceInterface::class);
        $productTypeService
            ->method('findProductTypes')
            ->with(new ProductTypeQuery(null, self::EXAMPLE_OFFSET, self::EXAMPLE_LIMIT))
            ->willReturn($expectedProductTypeList);

        $adapter = new ProductTypeListAdapter($productTypeService);

        self::assertEquals(
            new ArrayIterator($types),
            $adapter->fetch(self::EXAMPLE_OFFSET, self::EXAMPLE_LIMIT)
        );
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface[] $types
     */
    private function createProductTypesList(array $types): ProductTypeListInterface
    {
        $list = $this->createMock(ProductTypeListInterface::class);
        $list->method('getIterator')->willReturn(new ArrayIterator($types));

        return $list;
    }
}
