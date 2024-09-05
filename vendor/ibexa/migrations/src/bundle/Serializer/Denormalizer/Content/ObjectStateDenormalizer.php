<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Denormalizer\Content;

use Ibexa\Migration\ValueObject\Content\ObjectState;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class ObjectStateDenormalizer implements DenormalizerInterface, CacheableSupportsMethodInterface
{
    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        return ObjectState::createFromArray($data);
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return $type === ObjectState::class;
    }
}
