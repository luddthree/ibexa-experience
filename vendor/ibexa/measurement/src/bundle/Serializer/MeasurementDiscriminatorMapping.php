<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Measurement\Serializer;

use Ibexa\Bundle\ProductCatalog\Serializer\AttributeValueMappingFactoryInterface;
use Ibexa\Contracts\Measurement\Value\RangeValueInterface;
use Ibexa\Contracts\Measurement\Value\SimpleValueInterface;
use Ibexa\Contracts\Measurement\Value\ValueInterface;

final class MeasurementDiscriminatorMapping implements AttributeValueMappingFactoryInterface
{
    public function getMapping(): array
    {
        return [
            'measurement_range' => RangeValueInterface::class,
            'measurement_single' => SimpleValueInterface::class,
            'measurement' => ValueInterface::class,
        ];
    }
}
