<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Bundle\Migration\Serializer\Denormalizer\StepNormalizerInterface;
use Ibexa\Migration\ValueObject\Step\ReferenceLoadStep;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class ReferenceLoadStepNormalizer implements StepNormalizerInterface, DenormalizerInterface, NormalizerInterface, CacheableSupportsMethodInterface
{
    public function getType(): string
    {
        return 'reference';
    }

    public function getMode(): string
    {
        return 'load';
    }

    public function getHandledClassType(): string
    {
        return ReferenceLoadStep::class;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\ReferenceLoadStep $object
     * @param array<string, mixed> $context
     *
     * @return array{
     *     type: string,
     *     mode: string,
     *     filename: string,
     * }
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        Assert::isInstanceOf($object, ReferenceLoadStep::class);

        return [
            'type' => $this->getType(),
            'mode' => $this->getMode(),
            'filename' => $object->filename,
        ];
    }

    /**
     * @param array{
     *     type: string,
     *     mode: string,
     *     filename: string,
     * } $data
     * @param array<string, mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\ReferenceLoadStep
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        Assert::keyExists($data, 'filename');

        return new ReferenceLoadStep($data['filename']);
    }

    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof ReferenceLoadStep;
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return $type === ReferenceLoadStep::class;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class_alias(ReferenceLoadStepNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Step\ReferenceLoadStepNormalizer');
