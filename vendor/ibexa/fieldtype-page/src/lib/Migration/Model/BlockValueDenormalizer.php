<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Migration\Model;

use DateTime;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @internal
 */
final class BlockValueDenormalizer implements DenormalizerInterface, CacheableSupportsMethodInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return $type === BlockValue::class;
    }

    /**
     * @param array<mixed> $data
     * @param array<mixed> $context
     *
     * @throws \Exception
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function denormalize($data, string $type, string $format = null, array $context = []): BlockValue
    {
        $id = $data['id'];
        $blockType = $data['type'];
        $name = $data['name'];
        $view = $data['view'] ?? 'default';

        if (!isset($id, $blockType, $name, $view)) {
            throw new InvalidArgumentException(
                '$data',
                '"id", "type", "name", and "view" data for Page Block are required and cannot be null'
            );
        }

        $since = $data['since'] ?? null;
        $till = $data['till'] ?? null;

        return new BlockValue(
            $id,
            $blockType,
            $name,
            $view,
            $data['class'] ?? null,
            $data['style'] ?? null,
            $data['compiled'] ?? null,
            null !== $since ? $this->denormalizer->denormalize($since, DateTime::class, $format, $context) : null,
            null !== $till ? $this->denormalizer->denormalize($till, DateTime::class, $format, $context) : null,
            $this->denormalizer->denormalize($data['attributes'] ?? [], Attribute::class . '[]', $format, $context)
        );
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
