<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\AttributeConverter;

use Ibexa\Contracts\ProductCatalog\Personalization\AttributeConverterInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;
use Ibexa\Measurement\Value\SimpleValue;

final class MeasurementSimple implements AttributeConverterInterface
{
    public function accept(AttributeInterface $attribute): bool
    {
        return $attribute->getValue() instanceof SimpleValue;
    }

    public function convert(AttributeInterface $attribute): float
    {
        /** @var \Ibexa\Measurement\Value\SimpleValue $attributeValue */
        $attributeValue = $attribute->getValue();

        return $attributeValue->getValue();
    }
}
