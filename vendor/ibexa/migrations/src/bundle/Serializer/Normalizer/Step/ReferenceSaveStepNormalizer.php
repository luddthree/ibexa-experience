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

final class ReferenceSaveStepNormalizer implements StepNormalizerInterface, DenormalizerInterface, NormalizerInterface, CacheableSupportsMethodInterface
{
    public function getType(): string
    {
        return 'reference';
    }

    public function getMode(): string
    {
        return 'save';
    }

    public function getHandledClassType(): string
    {
        return ValueObject\Step\ReferenceSaveStep::class;
    }

    /**
     * @param array{
     *     type: string,
     *     mode: string,
     *     filename: string,
     * } $data
     * @param array<string, mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\ReferenceSaveStep
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        Assert::keyExists($data, 'filename');

        return new ValueObject\Step\ReferenceSaveStep(
            $data['filename']
        );
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return $type === ValueObject\Step\ReferenceSaveStep::class;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\ReferenceSaveStep $object
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
        Assert::isInstanceOf($object, ValueObject\Step\ReferenceSaveStep::class);

        return [
            'type' => $this->getType(),
            'mode' => $this->getMode(),
            'filename' => $object->filename,
        ];
    }

    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof ValueObject\Step\ReferenceSaveStep;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class_alias(ReferenceSaveStepNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Step\ReferenceSaveStepNormalizer');
