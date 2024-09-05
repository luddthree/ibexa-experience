<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Step\Role;

use Ibexa\Migration\ValueObject\Step\Role\UpdateMetadata;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class UpdateMetadataNormalizer implements NormalizerInterface, DenormalizerInterface, CacheableSupportsMethodInterface
{
    /**
     * @param mixed $data
     * @param array<string, mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\Role\UpdateMetadata
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        return new UpdateMetadata($data['identifier'] ?? null);
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return $type === UpdateMetadata::class;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\Role\UpdateMetadata $metadata
     * @param array<string, mixed> $context
     *
     * @return array{identifier: ?string}
     */
    public function normalize($metadata, string $format = null, array $context = [])
    {
        Assert::isInstanceOf($metadata, UpdateMetadata::class);

        return [
            'identifier' => $metadata->identifier,
        ];
    }

    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof UpdateMetadata;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class_alias(UpdateMetadataNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Step\Role\UpdateMetadataNormalizer');
