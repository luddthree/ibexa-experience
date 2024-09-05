<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Denormalizer\ContentType;

use function array_merge;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Migration\ValueObject\ContentType\CreateMetadata;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Webmozart\Assert\Assert;

final class ContentTypeCreateMetadataDenormalizer implements DenormalizerInterface, CacheableSupportsMethodInterface
{
    /** @var string */
    private $defaultLanguage;

    public function __construct(string $defaultLanguage)
    {
        $this->defaultLanguage = $defaultLanguage;
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $context
     */
    public function denormalize($data, string $type, string $format = null, array $context = []): CreateMetadata
    {
        $this->validateMetadata($data);

        $metadataValues = array_merge([
            'mainTranslation' => $this->defaultLanguage,
            'creatorId' => null,
            'remoteId' => null, // will be generated elsewhere
            'urlAliasSchema' => null,
            'nameSchema' => null,
            'container' => false,
            'defaultAlwaysAvailable' => true,
            'defaultSortField' => Location::SORT_FIELD_PUBLISHED,
            'defaultSortOrder' => Location::SORT_ORDER_DESC,
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
     * @param array<string, mixed> $data
     */
    private function validateMetadata(array $data): void
    {
        Assert::keyExists($data, 'contentTypeGroups');
        Assert::isArray($data['contentTypeGroups']);
        Assert::keyExists($data, 'translations');
        Assert::isArray($data['translations']);
    }
}

class_alias(ContentTypeCreateMetadataDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Denormalizer\ContentType\ContentTypeCreateMetadataDenormalizer');
