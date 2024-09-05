<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Step\Role;

use Ibexa\Migration\ValueObject\Step\Role\CreateMetadata;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class CreateMetadataNormalizer implements NormalizerInterface, DenormalizerInterface, CacheableSupportsMethodInterface
{
    /**
     * @param mixed $data
     * @param array<string, mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\Role\CreateMetadata
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        return new CreateMetadata($data['identifier']);
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return $type === CreateMetadata::class;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\Role\CreateMetadata $metadata
     * @param array<string, mixed> $context
     *
     * @return array{identifier: string}
     */
    public function normalize($metadata, string $format = null, array $context = [])
    {
        Assert::isInstanceOf($metadata, CreateMetadata::class);

        return [
            'identifier' => $metadata->identifier,
        ];
    }

    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof CreateMetadata;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class_alias(CreateMetadataNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Step\Role\CreateMetadataNormalizer');
