<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Content;

use DateTime;
use Ibexa\Migration\ValueObject\Content\CreateMetadata;
use Ibexa\Migration\ValueObject\Content\Metadata\Section;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class CreateMetadataNormalizer implements
    NormalizerInterface,
    DenormalizerInterface,
    NormalizerAwareInterface,
    DenormalizerAwareInterface,
    CacheableSupportsMethodInterface
{
    use DenormalizerAwareTrait;

    use NormalizerAwareTrait;

    /**
     * @param mixed $data
     * @param array<mixed> $context
     */
    public function denormalize($data, string $type, string $format = null, array $context = []): CreateMetadata
    {
        Assert::isArray($data);
        Assert::keyExists($data, 'contentType');
        Assert::keyExists($data, 'mainTranslation');

        $section = null;
        if (isset($data['section'])) {
            $section = $this->denormalizer->denormalize(
                $data['section'],
                Section::class,
                $format,
                $context
            );
        }

        $modificationDate = null;
        if (isset($data['modificationDate'])) {
            $modificationDate = $this->denormalizer->denormalize(
                $data['modificationDate'],
                DateTime::class,
                $format,
                $context
            );
        }

        $publicationDate = null;
        if (isset($data['publicationDate'])) {
            $publicationDate = $this->denormalizer->denormalize(
                $data['publicationDate'],
                DateTime::class,
                $format,
                $context
            );
        }

        return new CreateMetadata(
            $data['contentType'],
            $data['mainTranslation'],
            $data['creatorId'] ?? null,
            $modificationDate,
            $publicationDate,
            $data['remoteId'] ?? null,
            $data['alwaysAvailable'] ?? null,
            $section,
        );
    }

    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return $type === CreateMetadata::class;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Content\CreateMetadata $object
     * @param array<mixed> $context
     *
     * @return array<string, mixed|scalar|null>
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        $section = $this->normalizer->normalize($object->section, $format, $context);

        return [
            'contentType' => $object->contentType,
            'mainTranslation' => $object->mainTranslation,
            'creatorId' => $object->creatorId,
            'modificationDate' => $this->normalizer->normalize($object->modificationDate, $format, $context),
            'publicationDate' => $this->normalizer->normalize($object->publicationDate, $format, $context),
            'remoteId' => $object->remoteId,
            'alwaysAvailable' => $object->alwaysAvailable,
            'section' => $section,
        ];
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof CreateMetadata;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class_alias(CreateMetadataNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Content\CreateMetadataNormalizer');
