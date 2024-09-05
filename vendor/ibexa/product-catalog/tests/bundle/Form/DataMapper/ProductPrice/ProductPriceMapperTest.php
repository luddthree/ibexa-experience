<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataMapper\ProductPrice;

use Ibexa\Bundle\ProductCatalog\Form\Data\Price\Create\ProductPriceCreateData;
use Ibexa\Bundle\ProductCatalog\Form\DataMapper\ProductPrice\DataToStructTransformerInterface;
use Ibexa\Bundle\ProductCatalog\Form\DataMapper\ProductPrice\ProductPriceMapper;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\ProductPriceCreateStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\Form\DataMapper\ProductPrice\ProductPriceMapper
 */
final class ProductPriceMapperTest extends TestCase
{
    private ProductPriceMapper $mapper;

    /**
     * @var \Ibexa\Bundle\ProductCatalog\Form\DataMapper\ProductPrice\DataToStructTransformerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $dataToStructTransformer;

    protected function setUp(): void
    {
        $this->dataToStructTransformer = $this->createMock(DataToStructTransformerInterface::class);

        $notSupportingDataToStructTransformer = $this->createMock(DataToStructTransformerInterface::class);

        $notSupportingDataToStructTransformer
            ->expects(self::never())
            ->method('convertDataToStruct');

        $notSupportingDataToStructTransformer
            ->method('supports')
            ->willReturn(false);

        $this->mapper = new ProductPriceMapper(
            [
                $notSupportingDataToStructTransformer,
                $this->dataToStructTransformer,
            ],
        );
    }

    public function testReverseMap(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $currency = $this->createMock(CurrencyInterface::class);
        $expectedStruct = $this->createMock(ProductPriceCreateStructInterface::class);

        $data = new ProductPriceCreateData($product, $currency);

        $this->dataToStructTransformer
            ->expects(self::once())
            ->method('supports')
            ->with(self::identicalTo($data))
            ->willReturn(true);

        $this->dataToStructTransformer
            ->expects(self::once())
            ->method('convertDataToStruct')
            ->with(self::identicalTo($data))
            ->willReturn($expectedStruct);

        $struct = $this->mapper->mapToStruct($data);

        self::assertSame($expectedStruct, $struct);
    }
}
