<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Limitation;

use Ibexa\Migration\ValueObject\Step\Role\Limitation;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class LimitationNormalizer implements NormalizerInterface, DenormalizerInterface, CacheableSupportsMethodInterface
{
    public const CONTEXT_LIMITATION_TYPE = 'limitation_type';

    /**
     * @param \Ibexa\Migration\ValueObject\Step\Role\Limitation $limitation
     * @param array<string, mixed> $context
     *
     * @return array<mixed>
     */
    public function normalize($limitation, string $format = null, array $context = [])
    {
        Assert::isInstanceOf($limitation, Limitation::class);

        return [
            'identifier' => $limitation->identifier,
            'values' => $limitation->values,
        ];
    }

    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof Limitation;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function denormalize($data, string $type, string $format = null, array $context = []): Limitation
    {
        return new Limitation($data['identifier'], $data['values']);
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return $type === Limitation::class;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class_alias(LimitationNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Limitation\LimitationNormalizer');
