<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Migration\Model;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @internal
 */
final class AttributeDenormalizer implements DenormalizerInterface, CacheableSupportsMethodInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return $type === Attribute::class;
    }

    /**
     * @param array<mixed> $data
     * @param array<mixed> $context
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function denormalize($data, string $type, string $format = null, array $context = []): Attribute
    {
        $id = $data['id'] ?? null;
        $name = $data['name'] ?? null;
        $value = $data['value'] ?? null;

        if (!isset($id, $name, $value)) {
            throw new InvalidArgumentException(
                '$data',
                '"id", "name", and "value" data for Page Block Attribute are required and cannot be null'
            );
        }

        return new Attribute(
            $data['id'],
            $data['name'],
            $data['value']
        );
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
