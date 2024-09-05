<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Denormalizer\ContentTypeGroup;

use function array_merge;
use DateTime;
use Ibexa\Migration\ValueObject\ContentTypeGroup\UpdateMetadata;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Webmozart\Assert\Assert;

final class ContentTypeGroupUpdateMetadataDenormalizer implements DenormalizerInterface, CacheableSupportsMethodInterface
{
    /**
     * @param array<string, mixed> $context
     */
    public function denormalize($data, string $type, string $format = null, array $context = []): UpdateMetadata
    {
        $this->validateMetadata($data);

        $metadataValues = array_merge([
            'modifierId' => null,
            'modificationDate' => new DateTime('now'),
        ], $data);

        return UpdateMetadata::createFromArray($metadataValues);
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return UpdateMetadata::class === $type;
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

class_alias(ContentTypeGroupUpdateMetadataDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Denormalizer\ContentTypeGroup\ContentTypeGroupUpdateMetadataDenormalizer');
