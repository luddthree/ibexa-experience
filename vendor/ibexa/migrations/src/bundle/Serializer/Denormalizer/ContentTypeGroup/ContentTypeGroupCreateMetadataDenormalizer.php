<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Denormalizer\ContentTypeGroup;

use function array_merge;
use Ibexa\Migration\ValueObject\ContentTypeGroup\CreateMetadata;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Webmozart\Assert\Assert;

final class ContentTypeGroupCreateMetadataDenormalizer implements DenormalizerInterface, CacheableSupportsMethodInterface
{
    /**
     * @param array<string, mixed> $context
     */
    public function denormalize($data, string $type, string $format = null, array $context = []): CreateMetadata
    {
        $this->validateMetadata($data);

        $metadataValues = array_merge([
            'creatorId' => $data['creatorId'] ?? null,
            'creationDate' => $data['creationDate'] ?? date('c'),
        ], $data);

        return CreateMetadata::createFromArray($metadataValues);
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return CreateMetadata::class === $type;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }

    /**
     * @param array<mixed> $data
     */
    private function validateMetadata(array $data): void
    {
        Assert::keyExists($data, 'identifier');
        Assert::stringNotEmpty($data['identifier']);
    }
}

class_alias(ContentTypeGroupCreateMetadataDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Denormalizer\ContentTypeGroup\ContentTypeGroupCreateMetadataDenormalizer');
