<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer;

use Ibexa\Migration\ValueObject\Content\Location;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class LocationNormalizer implements NormalizerInterface, DenormalizerInterface, CacheableSupportsMethodInterface
{
    /**
     * @param \Ibexa\Migration\ValueObject\Content\Location $object
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        return [
            'parentLocationId' => $object->parentLocationId,
            'parentLocationRemoteId' => $object->parentLocationRemoteId,
            'locationRemoteId' => $object->remoteId,
            'hidden' => $object->hidden,
            'sortField' => $object->sortField,
            'sortOrder' => $object->sortOrder,
            'priority' => $object->priority,
        ];
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $data['remoteId'] = $data['locationRemoteId'] ?? null;
        unset($data['locationRemoteId']);

        return Location::createFromArray($data);
    }

    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof Location;
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return $type === Location::class;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class_alias(LocationNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\LocationNormalizer');
