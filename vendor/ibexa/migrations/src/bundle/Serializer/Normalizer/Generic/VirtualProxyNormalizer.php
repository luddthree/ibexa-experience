<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Generic;

use ProxyManager\Proxy\VirtualProxyInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class VirtualProxyNormalizer implements NormalizerInterface, NormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use NormalizerAwareTrait;

    /**
     * @param \ProxyManager\Proxy\VirtualProxyInterface $object
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        $object->initializeProxy();
        $value = $object->getWrappedValueHolderValue();

        return $this->normalizer->normalize(
            $value,
            $format,
            $context
        );
    }

    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof VirtualProxyInterface;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class_alias(VirtualProxyNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Generic\VirtualProxyNormalizer');
