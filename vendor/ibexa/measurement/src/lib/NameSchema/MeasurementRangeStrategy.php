<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\NameSchema;

use Ibexa\Contracts\Measurement\Value\RangeValueInterface;
use Ibexa\Contracts\ProductCatalog\NameSchema\NameSchemaStrategyInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;

final class MeasurementRangeStrategy implements NameSchemaStrategyInterface
{
    /**
     * @param mixed $value
     */
    public function resolve(AttributeDefinitionInterface $attributeDefinition, $value, string $languageCode): string
    {
        if ($value instanceof RangeValueInterface) {
            return $value->getMinValue() . '-' . $value->getMaxValue() . $value->getUnit()->getSymbol();
        }

        return '';
    }

    /**
     * @param mixed $value
     */
    public function supports(AttributeDefinitionInterface $attributeDefinition, $value): bool
    {
        return $attributeDefinition->getType()->getIdentifier() === 'measurement_range';
    }
}
