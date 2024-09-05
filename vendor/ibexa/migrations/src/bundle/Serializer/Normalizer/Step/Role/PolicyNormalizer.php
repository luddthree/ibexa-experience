<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Step\Role;

use Ibexa\Migration\ValueObject\Step\Role\Limitation;
use Ibexa\Migration\ValueObject\Step\Role\Policy;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class PolicyNormalizer implements NormalizerInterface, NormalizerAwareInterface, DenormalizerInterface, DenormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $limitations = $this->denormalizer->denormalize(
            $data['limitations'] ?? [],
            Limitation::class . '[]',
            $format,
            $context
        );

        return new Policy($data['module'], $data['function'], $limitations);
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return $type === Policy::class;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\Role\Policy $policy
     */
    public function normalize($policy, string $format = null, array $context = [])
    {
        Assert::isInstanceOf($policy, Policy::class);

        $normalized = [
            'module' => $policy->module,
            'function' => $policy->function,
        ];

        if (!empty($policy->limitations)) {
            $normalized['limitations'] = $this->normalizer->normalize($policy->limitations);
        }

        return $normalized;
    }

    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof Policy;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class_alias(PolicyNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Step\Role\PolicyNormalizer');
