<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Measurement\ProductCatalog\Form\Attribute\Search;

use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Contracts\Core\Search\FieldType\FloatField;
use Ibexa\Contracts\Measurement\Value\Definition\MeasurementInterface;
use Ibexa\Contracts\Measurement\Value\Definition\UnitInterface;
use Ibexa\Contracts\Measurement\Value\SimpleValueInterface;
use Ibexa\Measurement\ProductCatalog\Form\Attribute\Search\SimpleMeasurementIndexDataProvider;
use Ibexa\Measurement\UnitConverter\UnitConverterDispatcherInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\Attribute;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinition;
use PHPUnit\Framework\TestCase;
use Traversable;

/**
 * @covers \Ibexa\Measurement\ProductCatalog\Form\Attribute\Search\SimpleMeasurementIndexDataProvider
 */
final class SimpleMeasurementIndexDataProviderTest extends TestCase
{
    public function testGetFieldsForAttribute(): void
    {
        $unitConverter = $this->createMock(UnitConverterDispatcherInterface::class);
        $unitConverter->method('convert')
            ->willReturn($this->createMock(SimpleValueInterface::class));
        $provider = new SimpleMeasurementIndexDataProvider($unitConverter);

        $attributeDefinition = new AttributeDefinition();
        $attributeDefinition->identifier = '<ATTRIBUTE_DEFINITION_IDENTIFIER>';

        $measurementMock = $this->createMock(MeasurementInterface::class);
        $measurementMock
            ->method('getBaseUnit')
            ->willReturn($this->createMock(UnitInterface::class));

        $value = $this->createMock(SimpleValueInterface::class);
        $value
            ->method('getMeasurement')
            ->willReturn($measurementMock);

        $attribute = new Attribute(-1, -1, '', $value);

        $fields = $provider->getFieldsForAttribute($attributeDefinition, $attribute);

        if ($fields instanceof Traversable) {
            $fields = iterator_to_array($fields);
        }

        self::assertCount(1, $fields);
        self::assertContainsOnlyInstancesOf(Field::class, $fields);
        [ $field ] = $fields;

        self::assertSame('product_attribute_<ATTRIBUTE_DEFINITION_IDENTIFIER>_value', $field->getName());
        self::assertInstanceOf(FloatField::class, $field->getType());
    }
}
