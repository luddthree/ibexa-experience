<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\Serializer\Normalizer\FieldType;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Personalization\Serializer\Normalizer\DestinationValueAwareInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\FieldType\Image\Value as ImageValue;

final class ImageNormalizer implements DestinationValueAwareInterface
{
    public function normalize(Value $value): ?string
    {
        if (!$value instanceof ImageValue) {
            throw new InvalidArgumentType('$value', ImageValue::class);
        }

        return $value->uri;
    }

    public function supportsValue(Value $value): bool
    {
        return $value instanceof ImageValue;
    }
}
