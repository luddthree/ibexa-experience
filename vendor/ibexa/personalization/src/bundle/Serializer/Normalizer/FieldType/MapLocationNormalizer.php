<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\Serializer\Normalizer\FieldType;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Personalization\Serializer\Normalizer\ValueNormalizerInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\FieldType\MapLocation\Value as MapLocationValue;

final class MapLocationNormalizer implements ValueNormalizerInterface
{
    public function normalize(Value $value): ?string
    {
        if (!$value instanceof MapLocationValue) {
            throw new InvalidArgumentType('$value', MapLocationValue::class);
        }

        return $value->address;
    }

    public function supportsValue(Value $value): bool
    {
        return $value instanceof MapLocationValue;
    }
}
