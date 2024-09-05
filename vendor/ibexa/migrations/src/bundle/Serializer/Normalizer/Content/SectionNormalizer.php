<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Content;

use Ibexa\Migration\ValueObject\Content\Metadata\Section;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class SectionNormalizer implements NormalizerInterface, DenormalizerInterface, CacheableSupportsMethodInterface
{
    /**
     * @param mixed $data
     * @param array<mixed> $context
     */
    public function denormalize($data, string $type, string $format = null, array $context = []): Section
    {
        // BC Layer
        if (!is_array($data)) {
            $data = [
                'id' => $data,
            ];
        }

        Assert::nullOrInteger(
            $data['id'] ?? null,
            'Section ID must be integer or null'
        );
        Assert::nullOrStringNotEmpty(
            $data['identifier'] ?? null,
            'Section identifier must be non-empty string or null'
        );

        return new Section($data['id'] ?? null, $data['identifier'] ?? null);
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return $type === Section::class;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Content\Metadata\Section $object
     * @param array<mixed> $context
     *
     * @return array{id?: int, identifier?: string}
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
        return $data instanceof Section;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class_alias(SectionNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Content\SectionNormalizer');
