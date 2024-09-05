<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Segmentation\Serializer\Normalizer\SegmentGroup;

use Ibexa\Segmentation\Value\SegmentGroupMatcher;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class MatcherNormalizer implements NormalizerInterface, DenormalizerInterface, CacheableSupportsMethodInterface
{
    /**
     * @param array{
     *     id?: int,
     *     identifier?: string,
     * } $data
     * @param array<mixed> $context
     */
    public function denormalize($data, string $type, string $format = null, array $context = []): SegmentGroupMatcher
    {
        $id = null;
        $identifier = null;

        if (isset($data['id'])) {
            Assert::integer($data['id']);
            $id = $data['id'];
        }
        if (isset($data['identifier'])) {
            Assert::string($data['identifier']);
            $identifier = $data['identifier'];
        }

        return new SegmentGroupMatcher($id, $identifier);
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return $type === SegmentGroupMatcher::class;
    }

    /**
     * @param \Ibexa\Segmentation\Value\SegmentGroupMatcher $object
     * @param array<mixed> $context
     *
     * @return array{id?: int|null, identifier?: string|null}
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        $result = [];

        if ($object->getId() !== null) {
            $result['id'] = $object->getId();
        }

        if ($object->getIdentifier() !== null) {
            $result['identifier'] = $object->getIdentifier();
        }

        return $result;
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof SegmentGroupMatcher;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
