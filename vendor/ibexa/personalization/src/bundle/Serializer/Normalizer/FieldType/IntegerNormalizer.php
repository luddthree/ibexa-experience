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
use Ibexa\Core\FieldType\Integer\Value as IntegerValue;

final class IntegerNormalizer implements ValueNormalizerInterface
{
    public function normalize(Value $value): ?int
    {
        if (!$value instanceof IntegerValue) {
            throw new InvalidArgumentType('$value', IntegerValue::class);
        }

        return $value->value;
    }

    public function supportsValue(Value $value): bool
    {
        return $value instanceof IntegerValue;
    }
}
