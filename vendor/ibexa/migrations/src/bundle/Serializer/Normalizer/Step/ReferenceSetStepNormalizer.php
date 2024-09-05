<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Bundle\Migration\Serializer\Denormalizer\StepNormalizerInterface;
use Ibexa\Migration\ValueObject;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class ReferenceSetStepNormalizer implements StepNormalizerInterface, DenormalizerInterface, NormalizerInterface, CacheableSupportsMethodInterface
{
    public function getType(): string
    {
        return 'reference';
    }

    public function getMode(): string
    {
        return 'set';
    }

    public function getHandledClassType(): string
    {
        return ValueObject\Step\ReferenceSetStep::class;
    }

    /**
     * @param array{
     *     type: string,
     *     mode: string,
     *     name: string,
     *     value: int|string,
     * } $data
     * @param array<string, mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\ReferenceSetStep
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        Assert::keyExists($data, 'name');
        Assert::keyExists($data, 'value');

        return new ValueObject\Step\ReferenceSetStep(
            $data['name'],
            $data['value']
        );
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return $type === ValueObject\Step\ReferenceSetStep::class;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\ReferenceSetStep $object
     * @param array<string, mixed> $context
     *
     * @return array{
     *     type: string,
     *     mode: string,
     *     name: string,
     *     value: int|string
     * }
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        Assert::isInstanceOf($object, ValueObject\Step\ReferenceSetStep::class);

        return [
            'type' => $this->getType(),
            'mode' => $this->getMode(),
            'name' => $object->name,
            'value' => $object->value,
        ];
    }

    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof ValueObject\Step\ReferenceSetStep;
    }
}

class_alias(ReferenceSetStepNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Step\ReferenceSetStepNormalizer');
