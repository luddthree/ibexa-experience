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

final class ReferenceListStepNormalizer implements StepNormalizerInterface, DenormalizerInterface, CacheableSupportsMethodInterface
{
    public function getType(): string
    {
        return 'reference';
    }

    public function getMode(): string
    {
        return 'list';
    }

    public function getHandledClassType(): string
    {
        return ValueObject\Step\ReferenceListStep::class;
    }

    /**
     * @param array{
     *     type: string,
     *     mode: string,
     * } $data
     * @param array<string, mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\ReferenceListStep
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        return new ValueObject\Step\ReferenceListStep();
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return $type === ValueObject\Step\ReferenceListStep::class;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class_alias(ReferenceListStepNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Step\ReferenceListStepNormalizer');
