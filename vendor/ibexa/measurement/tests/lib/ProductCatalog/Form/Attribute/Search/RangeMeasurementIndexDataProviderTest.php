<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Measurement\ProductCatalog\Form\Attribute\Search;

use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Contracts\Core\Search\FieldType\FloatField;
use Ibexa\Contracts\Measurement\Value\RangeValueInterface;
use Ibexa\Measurement\ProductCatalog\Form\Attribute\Search\RangeMeasurementIndexDataProvider;
use Ibexa\Measurement\UnitConverter\UnitConverterDispatcherInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\Attribute;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinition;
use PHPUnit\Framework\TestCase;
use Traversable;

/**
 * @covers \Ibexa\Measurement\ProductCatalog\Form\Attribute\Search\RangeMeasurementIndexDataProvider
 */
final class RangeMeasurementIndexDataProviderTest extends TestCase
{
    public function testGetFieldsForAttribute(): void
    {
        $rangeValue = $this->createMock(RangeValueInterface::class);
        $unitConverterDispatcher = $this->createMock(UnitConverterDispatcherInterface::class);
        $unitConverterDispatcher
            ->method('convert')
            ->with(self::identicalTo($rangeValue))
            ->willReturn($rangeValue);

        $provider = new RangeMeasurementIndexDataProvider(
            $unitConverterDispatcher
        );

        $attributeDefinition = new AttributeDefinition();
        $attributeDefinition->identifier = '<ATTRIBUTE_DEFINITION_IDENTIFIER>';

        $attribute = new Attribute(-1, -1, '', $rangeValue);

        $fields = $provider->getFieldsForAttribute($attributeDefinition, $attribute);

        if ($fields instanceof Traversable) {
            $fields = iterator_to_array($fields);
        }

        self::assertCount(2, $fields);
        self::assertContainsOnlyInstancesOf(Field::class, $fields);
        [ $minField, $maxField ] = $fields;

        self::assertSame('product_attribute_<ATTRIBUTE_DEFINITION_IDENTIFIER>_min_value', $minField->getName());
        self::assertInstanceOf(FloatField::class, $minField->getType());

        self::assertSame('product_attribute_<ATTRIBUTE_DEFINITION_IDENTIFIER>_max_value', $maxField->getName());
        self::assertInstanceOf(FloatField::class, $maxField->getType());
    }
}
