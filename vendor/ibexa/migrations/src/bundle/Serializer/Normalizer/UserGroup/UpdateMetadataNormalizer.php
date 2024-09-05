<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\UserGroup;

use Ibexa\Migration\ValueObject\UserGroup\UpdateMetadata;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

/**
 * @phpstan-type TData array{
 *     alwaysAvailable: ?bool,
 *     mainLanguage: string,
 *     modificationDate: mixed,
 *     ownerId: ?int,
 *     parentGroupId: int,
 *     remoteId: ?string,
 *     sectionId: ?int,
 * }
 */
final class UpdateMetadataNormalizer implements NormalizerInterface, DenormalizerInterface, NormalizerAwareInterface, DenormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use DenormalizerAwareTrait;

    use NormalizerAwareTrait;

    public function __construct(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }

    /**
     * @phpstan-param TData $data
     */
    public function denormalize($data, string $type, ?string $format = null, array $context = []): UpdateMetadata
    {
        Assert::isArray($data);

        $modificationDate = null;
        if (isset($data['modificationDate'])) {
            $modificationDate = $this->denormalizer->denormalize(
                $data['modificationDate'],
                \DateTime::class,
                $format,
                $context,
            );
        }

        return new UpdateMetadata(
            $data['alwaysAvailable'] ?? null,
            $data['mainLanguage'] ?? null,
            $modificationDate,
            $data['ownerId'] ?? null,
            $data['parentGroupId'] ?? null,
            $data['remoteId'] ?? null,
            $data['sectionId'] ?? null
        );
    }

    public function supportsDenormalization($data, string $type, ?string $format = null): bool
    {
        return $type === UpdateMetadata::class;
    }

    /**
     * @phpstan-return TData
     */
    public function normalize($object, ?string $format = null, array $context = []): array
    {
        return [
            'alwaysAvailable' => $object->alwaysAvailable,
            'mainLanguage' => $object->mainLanguage,
            'modificationDate' => $this->normalizer->normalize($object->modificationDate, $format, $context),
            'ownerId' => $object->ownerId,
            'parentGroupId' => $object->parentGroupId,
            'remoteId' => $object->remoteId,
            'sectionId' => $object->sectionId,
        ];
    }

    public function supportsNormalization($data, ?string $format = null): bool
    {
        return $data instanceof UpdateMetadata;
    }
}
