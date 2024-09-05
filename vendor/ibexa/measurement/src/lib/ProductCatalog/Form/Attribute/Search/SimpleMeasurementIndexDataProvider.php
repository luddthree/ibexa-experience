<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Form\Attribute\Search;

use Ibexa\Contracts\Core\Search;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\IndexDataProviderInterface;
use Ibexa\Measurement\UnitConverter\UnitConverterDispatcherInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\Attribute;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinition;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\FieldNameBuilder\AttributeFieldNameBuilder;

/**
 * @template-implements \Ibexa\Contracts\ProductCatalog\Local\Attribute\IndexDataProviderInterface<
 *     \Ibexa\Contracts\Measurement\Value\SimpleValueInterface,
 * >
 */
final class SimpleMeasurementIndexDataProvider implements IndexDataProviderInterface
{
    private UnitConverterDispatcherInterface $unitConverterDispatcher;

    public function __construct(UnitConverterDispatcherInterface $unitConverterDispatcher)
    {
        $this->unitConverterDispatcher = $unitConverterDispatcher;
    }

    public function getFieldsForAttribute(AttributeDefinition $attributeDefinition, Attribute $attribute): iterable
    {
        $measurement = $attribute->getValue();

        if ($measurement === null) {
            // Attributes that are not required can be empty
            return [];
        }

        if (!$measurement->getUnit()->isBaseUnit()) {
            $measurement = $this->unitConverterDispatcher->convert(
                $measurement,
                $measurement->getMeasurement()->getBaseUnit()
            );
        }

        $fieldNameBuilder = new AttributeFieldNameBuilder($attributeDefinition->identifier);
        $fieldNameBuilder->withField('value');

        $value = $measurement->getValue();

        return [
            new Search\Field(
                $fieldNameBuilder->build(),
                $value,
                new Search\FieldType\FloatField(),
            ),
        ];
    }
}
