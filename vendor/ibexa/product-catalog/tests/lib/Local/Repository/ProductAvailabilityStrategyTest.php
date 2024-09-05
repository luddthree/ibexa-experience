<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository;

use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityContextInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductAvailability\HandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductAvailability;
use Ibexa\ProductCatalog\Local\Repository\Values\Availability;
use Ibexa\ProductCatalog\Local\Repository\Values\AvailabilityContext\AvailabilityContext;
use PHPUnit\Framework\TestCase;

final class ProductAvailabilityStrategyTest extends TestCase
{
    private const PRODUCT_CODE = '0001';
    private const PRODUCT_AVAILABILITY_ID = 1;

    /**
     * @dataProvider dataProviderForTestAcceptContext
     */
    public function testAcceptContext(
        AvailabilityContextInterface $availabilityContext,
        bool $expectedResult
    ): void {
        $strategy = new ProductAvailabilityStrategy(
            $this->createMock(HandlerInterface::class)
        );

        self::assertSame($strategy->accept($availabilityContext), $expectedResult);
    }

    /**
     * @return iterable<string,array{\Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityContextInterface, bool}>
     */
    public function dataProviderForTestAcceptContext(): iterable
    {
        yield 'accepted' => [
            new AvailabilityContext(),
            true,
        ];

        yield 'unsupported' => [
            new class() implements AvailabilityContextInterface {
                /* empty implementation */
            },
            false,
        ];
    }

    /**
     * @dataProvider dataProviderForTestGetProductAvailability
     */
    public function testGetProductAvailability(
        bool $productAvailability,
        bool $isInfinite,
        ?int $stock,
        AvailabilityContext $availabilityContext,
        bool $expectedProductAvailability,
        bool $expectedIsInfinite,
        ?int $expectedStock
    ): void {
        $product = $this->createMock(ProductInterface::class);
        $product->method('getCode')->willReturn('0001');

        $productAvailability = new ProductAvailability(
            self::PRODUCT_AVAILABILITY_ID,
            $productAvailability,
            $isInfinite,
            $stock,
            self::PRODUCT_CODE
        );

        $handler = $this->createMock(HandlerInterface::class);
        $handler
            ->expects(self::once())
            ->method('find')
            ->with($product->getCode())
            ->willReturn($productAvailability);

        $strategy = new ProductAvailabilityStrategy($handler);
        $availability = $strategy->getProductAvailability(
            $product,
            $availabilityContext
        );

        $expectedAvailability = new Availability(
            $product,
            $expectedProductAvailability,
            $expectedIsInfinite,
            $expectedStock
        );

        self::assertEquals($availability, $expectedAvailability);
    }

    /**
     * @phpstan-return iterable<string, array{
     *     bool,
     *     bool,
     *     int|null,
     *     AvailabilityContext,
     *     bool,
     *     bool,
     *     int|null,
     * }>
     */
    public function dataProviderForTestGetProductAvailability(): iterable
    {
        yield 'Check availability of one product. Product is available with stock = 10' => [
            true,
            false,
            10,
            new AvailabilityContext(),
            true,
            false,
            10,
        ];

        yield 'Check availability of eight products. Product is available with stock = 10' => [
            true,
            false,
            10,
            new AvailabilityContext(8),
            true,
            false,
            10,
        ];

        yield 'Check availability of twelve products. Product is available with stock = 10' => [
            true,
            false,
            10,
            new AvailabilityContext(12),
            false,
            false,
            10,
        ];

        yield 'Check availability of one product. Product is available with infinite stock' => [
            true,
            true,
            null,
            new AvailabilityContext(),
            true,
            true,
            null,
        ];

        yield 'Check availability of one product. Product is unavailable with infinite stock' => [
            false,
            true,
            null,
            new AvailabilityContext(),
            false,
            true,
            null,
        ];

        yield 'Check availability of nine products. Product is available with infinite stock' => [
            true,
            true,
            null,
            new AvailabilityContext(9),
            true,
            true,
            null,
        ];
    }
}
