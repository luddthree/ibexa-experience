<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Migration\Data;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @internal
 */
final class ZonesBlocksListDenormalizer implements DenormalizerInterface, CacheableSupportsMethodInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return $type === ZonesBlocksList::class;
    }

    /**
     * @param array<mixed> $data
     * @param array<mixed> $context
     *
     * @throws \Exception
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function denormalize($data, string $type, string $format = null, array $context = []): ZonesBlocksList
    {
        if (!is_array($data)) {
            throw new InvalidArgumentException(
                '$data',
                'zones blocks list needs to be an array'
            );
        }
        $zonesBlocksList = new ZonesBlocksList();
        foreach ($data as $zoneData) {
            $this->validateZoneData($zoneData);
            $zonesBlocksList->addZoneBlocks(
                $zoneData['name'],
                $this->denormalizer->denormalize($zoneData['blocks'], BlockValue::class . '[]', $format, $context)
            );
        }

        return $zonesBlocksList;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }

    /**
     * @param array<mixed> $zoneData
     *
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    private function validateZoneData(array $zoneData): void
    {
        if (!isset($zoneData['name'])) {
            throw new InvalidArgumentException(
                '$data',
                'zone data needs to contain zone name'
            );
        }

        if (!isset($zoneData['blocks']) || !is_array($zoneData['blocks'])) {
            throw new InvalidArgumentException(
                '$data',
                "Zone {$zoneData['name']} list of blocks needs to be a non-empty array"
            );
        }
    }
}
