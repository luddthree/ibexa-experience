<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\Persistence\Serializer;

use Ibexa\Contracts\Core\Collection\ArrayMap;
use Ibexa\Contracts\Core\Collection\MapInterface;
use LogicException;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ContextNormalizer implements NormalizerAwareInterface, DenormalizerAwareInterface, NormalizerInterface, ContextAwareDenormalizerInterface
{
    use NormalizerAwareTrait;
    use DenormalizerAwareTrait;

    /**
     * @param array<string, mixed> $context
     *
     * @return array<int|string, array<string, mixed>>
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        $normalized = [];
        foreach ($object as $key => $data) {
            if (is_object($data)) {
                $normalized[$key] = [
                    'type' => get_class($data),
                    'data' => $this->normalizer->normalize($data, $format, $context),
                ];
            } else {
                $normalized[$key] = $data;
            }
        }

        return $normalized;
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof MapInterface;
    }

    /**
     * @param array<string, mixed> $context
     *
     * @phpstan-return \Ibexa\Contracts\Core\Collection\ArrayMap<array-key, mixed>
     */
    public function denormalize($data, string $type, string $format = null, array $context = []): ArrayMap
    {
        if ($data === null) {
            return new ArrayMap();
        }

        if (!is_array($data)) {
            throw new LogicException('Context deserialized datatype is not an array');
        }

        $data = array_map(
            function ($item) use ($format, $context) {
                if (!isset($item['type'])) {
                    return $item;
                }

                /** @phpstan-ignore-next-line */
                if (!$this->denormalizer->supportsDenormalization($item['data'], $item['type'], $format, $context)) {
                    return $item;
                }

                return $this->denormalizer->denormalize(
                    $item['data'],
                    $item['type'],
                    $format,
                    $context
                );
            },
            $data,
        );

        return new ArrayMap($data);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supportsDenormalization($data, string $type, string $format = null, array $context = []): bool
    {
        return $type === MapInterface::class;
    }
}
