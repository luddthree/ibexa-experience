<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataMapper;

use Ibexa\Bundle\ProductCatalog\Form\Data\Availability\ProductAvailabilityUpdateData;
use Ibexa\Bundle\ProductCatalog\Form\DataMapper\ProductAvailabilityUpdateMapper;
use Ibexa\Contracts\ProductCatalog\Values\AvailabilityAwareInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\Form\DataMapper\ProductAvailabilityUpdateMapper
 */
final class ProductAvailabilityUpdateMapperTest extends TestCase
{
    /** @dataProvider providerForTestMap */
    public function testMap(
        ProductAvailabilityUpdateData $updateData,
        bool $expectedAvailability,
        bool $expectedIsInfinite,
        ?int $expectedStock
    ): void {
        $mapper = new ProductAvailabilityUpdateMapper();
        $updateStruct = $mapper->mapToStruct($updateData);

        self::assertSame($expectedAvailability, $updateStruct->getAvailability());
        self::assertSame($expectedIsInfinite, $updateStruct->isInfinite());
        self::assertSame($expectedStock, $updateStruct->getStock());
    }

    /**
     * @return iterable<string, array{
     *     \Ibexa\Bundle\ProductCatalog\Form\Data\Availability\ProductAvailabilityUpdateData,
     *     bool,
     *     bool,
     *     int|null
     * }>
     */
    public function providerForTestMap(): iterable
    {
        yield 'Available, Stock set (100)' => [
            $this->getUpdateData(true, false, 100),
            true,
            false,
            100,
        ];

        yield 'Available, Stock infinite and set (100)' => [
            $this->getUpdateData(true, true, 100),
            true,
            true,
            null,
        ];

        yield 'Unavailable, Stock infinite and set (100)' => [
            $this->getUpdateData(false, true, null),
            false,
            true,
            null,
        ];
    }

    private function getUpdateData(
        bool $available,
        bool $isInfinite,
        ?int $stock
    ): ProductAvailabilityUpdateData {
        $data = new ProductAvailabilityUpdateData(
            $this->createMock(AvailabilityAwareInterface::class)
        );
        $data->setAvailable($available);
        $data->setInfinite($isInfinite);
        $data->setStock($stock);

        return $data;
    }
}
