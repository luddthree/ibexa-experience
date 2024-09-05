<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Step\Role;

use Ibexa\Migration\ValueObject\Step\Role\Policy;
use Ibexa\Migration\ValueObject\Step\Role\PolicyList;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class PolicyListNormalizer implements NormalizerInterface, NormalizerAwareInterface, DenormalizerInterface, DenormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use DenormalizerAwareTrait;

    use NormalizerAwareTrait;

    /**
     * @param mixed $data
     * @param array<mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\Role\PolicyList
     */
    public function denormalize($data, string $type, string $format = null, array $context = []): PolicyList
    {
        Assert::isArray($data);

        if (isset($data['list'])) {
            $policies = $this->denormalizer->denormalize($data['list'], Policy::class . '[]', $format, $context);
            $mode = $data['mode'] ?? PolicyList::MODE_REPLACE;
        } else {
            $policies = $this->denormalizer->denormalize($data, Policy::class . '[]', $format, $context);
            $mode = PolicyList::MODE_REPLACE;
        }

        return new PolicyList($policies, $mode);
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return $type === PolicyList::class;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\Role\PolicyList $object
     * @param array<mixed> $context
     *
     * @return array{mode: string, list: mixed}
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        assert($object instanceof PolicyList);

        return [
            'mode' => $object->getMode(),
            'list' => $this->normalizer->normalize($object->getPolicies(), $format, $context),
        ];
    }

    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof PolicyList;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
