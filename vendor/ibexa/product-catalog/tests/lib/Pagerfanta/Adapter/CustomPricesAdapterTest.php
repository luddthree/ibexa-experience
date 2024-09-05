<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Pagerfanta\Adapter;

use Ibexa\Bundle\ProductCatalog\UI\CustomPrice;
use Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomPriceAwareInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\ProductCatalog\Pagerfanta\Adapter\CustomPricesAdapter;
use Pagerfanta\Adapter\AdapterInterface;
use PHPUnit\Framework\TestCase;

final class CustomPricesAdapterTest extends TestCase
{
    private const EXAMPLE_OFFSET = 10;
    private const EXAMPLE_LIMIT = 100;
    private const EXAMPLE_NB_RESULTS = 100;

    /** @var \Pagerfanta\Adapter\AdapterInterface<\Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface>|\PHPUnit\Framework\MockObject\MockObject */
    private AdapterInterface $innerAdapter;

    /** @var \Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ProductPriceServiceInterface $priceService;

    /** @var \Ibexa\Contracts\ProductCatalog\Values\PriceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private PriceInterface $mainPrice;

    private CustomPricesAdapter $adapter;

    protected function setUp(): void
    {
        $this->innerAdapter = $this->createMock(AdapterInterface::class);
        $this->priceService = $this->createMock(ProductPriceServiceInterface::class);
        $this->mainPrice = $this->createMock(PriceInterface::class);

        $this->adapter = new CustomPricesAdapter($this->innerAdapter, $this->priceService, $this->mainPrice);
    }

    public function testGetNbResults(): void
    {
        $this->innerAdapter
            ->expects(self::once())
            ->method('getNbResults')
            ->willReturn(self::EXAMPLE_NB_RESULTS);

        self::assertEquals(
            self::EXAMPLE_NB_RESULTS,
            $this->adapter->getNbResults()
        );
    }

    public function testGetSlice(): void
    {
        $priceA = $this->createMock(CustomPriceAwareInterface::class);
        $priceB = $this->createMock(CustomPriceAwareInterface::class);
        $priceC = $this->createMock(CustomPriceAwareInterface::class);

        $customerGroupA = $this->createMock(CustomerGroupInterface::class);
        $customerGroupB = $this->createMock(CustomerGroupInterface::class);
        $customerGroupC = $this->createMock(CustomerGroupInterface::class);

        $results = [
            $this->createMock(CustomerGroupInterface::class),
            $this->createMock(CustomerGroupInterface::class),
            $this->createMock(CustomerGroupInterface::class),
        ];

        $this->innerAdapter
            ->method('getSlice')
            ->with(self::EXAMPLE_OFFSET, self::EXAMPLE_LIMIT)
            ->willReturn($results);

        $this->priceService
            ->method('findOneForCustomerGroup')
            ->withConsecutive(
                [$this->mainPrice, $customerGroupA],
                [$this->mainPrice, $customerGroupB],
                [$this->mainPrice, $customerGroupC]
            )
            ->willReturnOnConsecutiveCalls($priceA, $priceB, $priceC);

        $actualResults = $this->adapter->getSlice(self::EXAMPLE_OFFSET, self::EXAMPLE_LIMIT);

        self::assertEquals([
            new CustomPrice($customerGroupA, $priceA),
            new CustomPrice($customerGroupB, $priceB),
            new CustomPrice($customerGroupC, $priceC),
        ], $actualResults);
    }
}
