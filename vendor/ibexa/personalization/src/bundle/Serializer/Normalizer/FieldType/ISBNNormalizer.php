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
use Ibexa\Core\FieldType\ISBN\Value as ISBNValue;

final class ISBNNormalizer implements ValueNormalizerInterface
{
    public function normalize(Value $value): string
    {
        if (!$value instanceof ISBNValue) {
            throw new InvalidArgumentType('$value', ISBNValue::class);
        }

        return $value->isbn;
    }

    public function supportsValue(Value $value): bool
    {
        return $value instanceof ISBNValue;
    }
}
