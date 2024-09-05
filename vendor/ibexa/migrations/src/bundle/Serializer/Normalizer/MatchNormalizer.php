<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer;

use Ibexa\Migration\ValueObject\AbstractMatcher;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class MatchNormalizer implements NormalizerInterface, DenormalizerInterface, CacheableSupportsMethodInterface
{
    /**
     * @param \Ibexa\Migration\ValueObject\AbstractMatcher $object
     * @param array<string, mixed> $context
     *
     * @return array{
     *     field: mixed,
     *     value: mixed,
     * }
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        return [
            'field' => $object->field,
            'value' => $object->value,
        ];
    }

    /**
     * @param array<mixed> $context
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        return new $type(
            $data['field'],
            $data['value']
        );
    }

    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof AbstractMatcher;
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        if (!class_exists($type)) {
            return false;
        }

        return is_subclass_of($type, AbstractMatcher::class, true);
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class_alias(MatchNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\MatchNormalizer');
