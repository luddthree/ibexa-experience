<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\REST\Serializer;

use ArrayObject;
use Ibexa\ActivityLog\REST\Value\ActivityLogData;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ActivityLogDataNormalizer implements NormalizerInterface, NormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use NormalizerAwareTrait;

    /**
     * @param \Ibexa\ActivityLog\REST\Value\ActivityLogData $object
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        if (count($object->data->toArray()) === 0) {
            return new ArrayObject();
        }

        return $this->normalizer->normalize($object->data->toArray(), $format, $context);
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof ActivityLogData;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
