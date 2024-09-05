<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\AttributeConverter;

use Ibexa\Contracts\ProductCatalog\Personalization\AttributeConverterInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;
use Ibexa\Measurement\Value\RangeValue;

final class MeasurementRange implements AttributeConverterInterface
{
    public function accept(AttributeInterface $attribute): bool
    {
        return $attribute->getValue() instanceof RangeValue;
    }

    /**
     * @return array{float, float}
     */
    public function convert(AttributeInterface $attribute): array
    {
        /** @var \Ibexa\Measurement\Value\RangeValue $value */
        $value = $attribute->getValue();

        return [$value->getMinValue(), $value->getMaxValue()];
    }
}
