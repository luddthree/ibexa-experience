<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Pagerfanta\Adapter;

use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductVariantListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductVariantQuery;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Pagerfanta\Adapter\ProductVariantListAdapter;
use PHPUnit\Framework\TestCase;

final class ProductVariantListAdapterTest extends TestCase
{
    private const EXAMPLE_OFFSET = 10;
    private const EXAMPLE_LIMIT = 100;
    private const EXAMPLE_TOTAL_COUNT = 100;

    /** @var \Ibexa\Contracts\ProductCatalog\ProductServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ProductServiceInterface $productService;

    /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ProductInterface $product;

    private ProductVariantListAdapter $adapter;

    protected function setUp(): void
    {
        $this->productService = $this->createMock(ProductServiceInterface::class);
        $this->product = $this->createMock(ProductInterface::class);

        $this->adapter = new ProductVariantListAdapter(
            $this->productService,
            $this->product
        );
    }

    public function testGetSlice(): void
    {
        $expectedVariantsList = $this->createMock(ProductVariantListInterface::class);

        $this->productService
            ->method('findProductVariants')
            ->with(
                $this->product,
                new ProductVariantQuery(
                    self::EXAMPLE_OFFSET,
                    self::EXAMPLE_LIMIT
                )
            )
            ->willReturn($expectedVariantsList);

        $actualVariantsList = $this->adapter->getSlice(self::EXAMPLE_OFFSET, self::EXAMPLE_LIMIT);

        self::assertSame($expectedVariantsList, $actualVariantsList);
    }

    public function testGetNbResults(): void
    {
        $variantsList = $this->createMock(ProductVariantListInterface::class);
        $variantsList->method('getTotalCount')->willReturn(self::EXAMPLE_TOTAL_COUNT);

        $this->productService
            ->method('findProductVariants')
            ->with(
                $this->product,
                new ProductVariantQuery(0, 0)
            )
            ->willReturn($variantsList);

        self::assertEquals(
            self::EXAMPLE_TOTAL_COUNT,
            $this->adapter->getNbResults()
        );
    }
}
