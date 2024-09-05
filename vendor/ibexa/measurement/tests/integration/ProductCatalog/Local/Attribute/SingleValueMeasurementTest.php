<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Measurement\ProductCatalog\Local\Attribute;

use Ibexa\Contracts\Measurement\Value\SimpleValueInterface;
use Ibexa\Contracts\ProductCatalog\AttributeTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;
use Traversable;

final class SingleValueMeasurementTest extends AbstractValueMeasurementTest
{
    public function testSavingMeasurementAttribute(): void
    {
        $attributeTypeService = self::getServiceByClassName(AttributeTypeServiceInterface::class);
        $attributeType = $attributeTypeService->getAttributeType('measurement_single');

        $attributeGroup = $this->createAttributeGroup('foo');
        $attributeDefinition1 = $this->createAttributeDefinition(
            $attributeType,
            $attributeGroup,
            'foo_measurement_1_single_value',
            ['sign' => 'none'],
        );
        $attributeDefinition2 = $this->createAttributeDefinition(
            $attributeType,
            $attributeGroup,
            'foo_measurement_2_single_value',
            ['sign' => 'none'],
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

        $value1 = $this->measurementService->buildSimpleValue('length', 0.7, 'centimeter');
        $value2 = $this->measurementService->buildSimpleValue('length', 100, 'meter');

        $createStruct->setAttribute('foo_measurement_1_single_value', $value1);
        $createStruct->setAttribute('foo_measurement_2_single_value', $value2);
        $createStruct->setField('name', 'foo');

        $product = $this->productService->createProduct($createStruct);

        $attributes = $product->getAttributes();

        if ($attributes instanceof Traversable) {
            $attributes = iterator_to_array($attributes);
        }

        self::assertCount(2, $attributes);
        self::assertContainsOnlyInstancesOf(AttributeInterface::class, $attributes);

        self::assertSimpleAttributeShape(
            $attributes['foo_measurement_1_single_value'],
            'foo_measurement_1_single_value',
            'length',
            'centimeter',
            0.7
        );

        self::assertSimpleAttributeShape(
            $attributes['foo_measurement_2_single_value'],
            'foo_measurement_2_single_value',
            'length',
            'meter',
            100
        );
    }

    private static function assertSimpleAttributeShape(
        AttributeInterface $attribute,
        string $identifier,
        string $measurementType,
        string $measurementUnit,
        float $value
    ): void {
        self::assertSame($identifier, $attribute->getIdentifier());
        $simpleValue = $attribute->getValue();
        self::assertInstanceOf(SimpleValueInterface::class, $simpleValue);
        self::assertSame($measurementType, $simpleValue->getMeasurement()->getName());
        self::assertSame($measurementUnit, $simpleValue->getUnit()->getIdentifier());
        self::assertSame($value, $simpleValue->getValue());
    }
}
