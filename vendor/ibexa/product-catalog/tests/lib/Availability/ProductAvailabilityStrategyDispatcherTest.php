<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Availability;

use ArrayIterator;
use Ibexa\Contracts\ProductCatalog\ProductAvailabilityStrategyInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityContextInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\ProductCatalog\Availability\ProductAvailabilityStrategyDispatcher;
use PHPUnit\Framework\TestCase;

final class ProductAvailabilityStrategyDispatcherTest extends TestCase
{
    public function testDispatch(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $availabilityContext = $this->createMock(AvailabilityContextInterface::class);
        $availability = $this->createMock(AvailabilityInterface::class);
        $strategyA = $this->createStrategyMock();
        $strategyB = $this->createStrategyMock(true, $availability, $product, $availabilityContext);

        $strategies = new ArrayIterator([$strategyA, $strategyB]);
        $productAvailabilityStrategyDispatcher = new ProductAvailabilityStrategyDispatcher(
            $strategies
        );

        $result = $productAvailabilityStrategyDispatcher->dispatch(
            $product,
            $availabilityContext
        );

        self::assertSame($availability, $result);
    }

    public function testDispatchWillThrowInvalidArgumentException(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $availabilityContext = $this->createMock(AvailabilityContextInterface::class);

        $strategyA = $this->createStrategyMock();
        $strategyB = $this->createStrategyMock();

        $strategies = new ArrayIterator([$strategyA, $strategyB]);
        $productAvailabilityStrategyDispatcher = new ProductAvailabilityStrategyDispatcher(
            $strategies
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                "Argument 'availabilityContext' is invalid: Unable to find ProductAvailability that can accept %s",
                get_class($availabilityContext)
            )
        );

        $productAvailabilityStrategyDispatcher->dispatch(
            $product,
            $availabilityContext
        );
    }

    private function createStrategyMock(
        bool $accept = false,
        ?AvailabilityInterface $availability = null,
        ?ProductInterface $product = null,
        ?AvailabilityContextInterface $availabilityContext = null
    ): ProductAvailabilityStrategyInterface {
        $strategy = $this->createMock(ProductAvailabilityStrategyInterface::class);
        $strategy
            ->expects(self::once())
            ->method('accept')
            ->willReturn($accept);

        if ($availability !== null) {
            $strategy
                ->expects(self::once())
                ->method('getProductAvailability')
                ->with($product, $availabilityContext)
                ->willReturn($availability);
        }

        return $strategy;
    }
}
