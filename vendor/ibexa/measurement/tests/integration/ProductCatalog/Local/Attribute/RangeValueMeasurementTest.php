<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Measurement\ProductCatalog\Local\Attribute;

use Ibexa\Contracts\Measurement\Value\RangeValueInterface;
use Ibexa\Contracts\ProductCatalog\AttributeTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;
use Traversable;

final class RangeValueMeasurementTest extends AbstractValueMeasurementTest
{
    public function testSavingMeasurementAttribute(): void
    {
        $attributeTypeService = self::getServiceByClassName(AttributeTypeServiceInterface::class);
        $attributeType = $attributeTypeService->getAttributeType('measurement_range');

        $attributeGroup = $this->createAttributeGroup('foo');
        $attributeDefinition1 = $this->createAttributeDefinition(
            $attributeType,
            $attributeGroup,
            'foo_measurement_1_range_value'
        );
        $attributeDefinition2 = $this->createAttributeDefinition(
            $attributeType,
            $attributeGroup,
            'foo_measurement_2_range_value'
        );
        $productType = $this->createProductType(
            [
                $attributeDefinition1,
                $attributeDefinition2,
            ],
            'foo'
        );

        $createStruct = $this->productService->newProductCreateStruct($productType, 'eng-GB');
        $createStruct->setCode('foo_code');

        $value1 = $this->measurementService->buildRangeValue('length', 0.7, 0.9, 'centimeter');
        $value2 = $this->measurementService->buildRangeValue('length', 0.7, 0.9, 'meter');

        $createStruct->setAttribute('foo_measurement_1_range_value', $value1);
        $createStruct->setAttribute('foo_measurement_2_range_value', $value2);
        $createStruct->setField('name', 'foo');

        $product = $this->productService->createProduct($createStruct);

        $attributes = $product->getAttributes();

        if ($attributes instanceof Traversable) {
            $attributes = iterator_to_array($attributes);
        }

        self::assertCount(2, $attributes);
        self::assertContainsOnlyInstancesOf(AttributeInterface::class, $attributes);

        self::assertRangeAttributeShape(
            $attributes['foo_measurement_1_range_value'],
            'foo_measurement_1_range_value',
            'length',
            'centimeter',
            0.7,
            0.9
        );

        self::assertRangeAttributeShape(
            $attributes['foo_measurement_2_range_value'],
            'foo_measurement_2_range_value',
            'length',
            'meter',
            0.7,
            0.9
        );
    }

    private static function assertRangeAttributeShape(
        AttributeInterface $attribute,
        string $identifier,
        string $measurementType,
        string $measurementUnit,
        float $minValue,
        float $maxValue
    ): void {
        self::assertSame($identifier, $attribute->getIdentifier());
        $value = $attribute->getValue();
        self::assertInstanceOf(RangeValueInterface::class, $value);
        self::assertSame($measurementType, $value->getMeasurement()->getName());
        self::assertSame($measurementUnit, $value->getUnit()->getIdentifier());
        self::assertSame($minValue, $value->getMinValue());
        self::assertSame($maxValue, $value->getMaxValue());
    }
}
