<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Segmentation\Serializer\Normalizer\Segment;

use Ibexa\Segmentation\Value\SegmentMatcher;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class MatcherNormalizer implements NormalizerInterface, DenormalizerInterface, CacheableSupportsMethodInterface
{
    /**
     * @param array{
     *      id?: int,
     *      identifier?: string,
     * } $data
     * @param array<mixed> $context
     */
    public function denormalize($data, string $type, string $format = null, array $context = []): SegmentMatcher
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

        return new SegmentMatcher($id, $identifier);
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return $type === SegmentMatcher::class;
    }

    /**
     * @param \Ibexa\Segmentation\Value\SegmentMatcher $object
     * @param array<mixed> $context
     *
     * @return array{id?: int|string, identifier?: string, group?: mixed}
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
        return $data instanceof SegmentMatcher;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
