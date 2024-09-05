<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataMapper;

use Ibexa\Bundle\ProductCatalog\Form\Data\Availability\ProductAvailabilityCreateData;
use Ibexa\Bundle\ProductCatalog\Form\DataMapper\ProductAvailabilityCreateMapper;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\Form\DataMapper\ProductAvailabilityCreateMapper
 */
final class ProductAvailabilityCreateMapperTest extends TestCase
{
    public function testMap(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $data = new ProductAvailabilityCreateData($product);
        $data->setAvailable(true);
        $data->setInfinite(false);
        $data->setStock(100);

        $mapper = new ProductAvailabilityCreateMapper();
        $result = $mapper->mapToStruct($data);

        self::assertSame($product, $result->getProduct());
        self::assertTrue($result->getAvailability());
        self::assertSame(100, $result->getStock());
    }

    public function testMapWithInfiniteStock(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $data = new ProductAvailabilityCreateData($product);
        $data->setAvailable(true);
        $data->setInfinite(true);
        $data->setStock(100);

        $mapper = new ProductAvailabilityCreateMapper();
        $result = $mapper->mapToStruct($data);

        self::assertSame($product, $result->getProduct());
        self::assertTrue($result->getAvailability());
        self::assertNull($result->getStock());
    }
}
