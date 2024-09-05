<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer;
use Ibexa\Migration\ValueObject\Location\Matcher;
use Ibexa\Migration\ValueObject\Step\LocationSwapStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<
 *     \Ibexa\Migration\ValueObject\Step\LocationSwapStep
 * >
 */
final class LocationSwapStepNormalizer extends AbstractStepNormalizer
{
    public function getType(): string
    {
        return 'location';
    }

    public function getMode(): string
    {
        return 'swap';
    }

    public function getHandledClassType(): string
    {
        return LocationSwapStep::class;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\LocationSwapStep $object
     * @param array<string, mixed> $context
     *
     * @return array{
     *     match1: mixed,
     *     match2: mixed,
     * }
     */
    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        return [
            'match1' => $this->normalizer->normalize($object->match1, $format, $context),
            'match2' => $this->normalizer->normalize($object->match2, $format, $context),
        ];
    }

    /**
     * @param array{
     *     match1: array<mixed>,
     *     match2: array<mixed>,
     * } $data
     * @param array<string, mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\LocationSwapStep
     */
    protected function denormalizeStep($data, string $type, string $format = null, array $context = []): StepInterface
    {
        $match1 = $this->denormalizer->denormalize(
            $data['match1'],
            Matcher::class,
            $format,
            $context
        );

        $match2 = $this->denormalizer->denormalize(
            $data['match2'],
            Matcher::class,
            $format,
            $context
        );

        return new LocationSwapStep(
            $match1,
            $match2,
        );
    }
}
