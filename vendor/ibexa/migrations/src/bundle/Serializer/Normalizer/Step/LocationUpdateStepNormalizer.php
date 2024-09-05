<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\ValueObject\Location\Matcher;
use Ibexa\Migration\ValueObject\Location\UpdateMetadata;
use Ibexa\Migration\ValueObject\Step\LocationUpdateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<
 *     \Ibexa\Migration\ValueObject\Step\LocationUpdateStep
 * >
 */
final class LocationUpdateStepNormalizer extends AbstractStepNormalizer
{
    public function getType(): string
    {
        return 'location';
    }

    public function getMode(): string
    {
        return Mode::UPDATE;
    }

    public function getHandledClassType(): string
    {
        return LocationUpdateStep::class;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\LocationUpdateStep $object
     * @param array<string, mixed> $context
     *
     * @return array{
     *     match: mixed,
     *     metadata: mixed,
     * }
     */
    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        return [
            'match' => $this->normalizer->normalize($object->match, $format, $context),
            'metadata' => $this->normalizer->normalize($object->metadata, $format, $context),
        ];
    }

    /**
     * @param array{
     *     match: array,
     *     metadata: array,
     * } $data
     * @param array<string, mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\LocationUpdateStep
     */
    protected function denormalizeStep($data, string $type, string $format = null, array $context = []): StepInterface
    {
        $match = $this->denormalizer->denormalize(
            $data['match'],
            Matcher::class,
            $format,
            $context
        );

        return new LocationUpdateStep(
            UpdateMetadata::createFromArray($data['metadata']),
            $match
        );
    }
}

class_alias(LocationUpdateStepNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Step\LocationUpdateStepNormalizer');
