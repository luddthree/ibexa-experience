<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Segmentation\Serializer\Normalizer\Step;

use Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Segmentation\Value\SegmentGroupMatcher;
use Ibexa\Segmentation\Value\Step\SegmentGroupDeleteStep;
use Webmozart\Assert\Assert;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<
 *     \Ibexa\Segmentation\Value\Step\SegmentGroupDeleteStep
 * >
 */
final class SegmentGroupDeleteStepNormalizer extends AbstractStepNormalizer
{
    public function getType(): string
    {
        return 'segment_group';
    }

    public function getMode(): string
    {
        return Mode::DELETE;
    }

    public function getHandledClassType(): string
    {
        return SegmentGroupDeleteStep::class;
    }

    /**
     * @param \Ibexa\Segmentation\Value\Step\SegmentGroupDeleteStep $object
     * @param array<string, mixed> $context
     *
     * @return array{
     *     matcher: mixed,
     * }
     */
    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        return [
            'matcher' => $this->normalizer->normalize($object->getMatcher(), $format, $context),
        ];
    }

    /**
     * @param array{
     *     matcher: \Ibexa\Segmentation\Value\SegmentGroupMatcher,
     * } $data
     * @param array<string, mixed> $context
     */
    protected function denormalizeStep($data, string $type, string $format = null, array $context = []): StepInterface
    {
        Assert::isArray($data);
        Assert::keyExists($data, 'matcher');

        /** @var \Ibexa\Segmentation\Value\SegmentGroupMatcher $matcher */
        $matcher = $this->denormalizer->denormalize($data['matcher'], SegmentGroupMatcher::class, $format, $context);

        return new SegmentGroupDeleteStep(
            $matcher,
        );
    }
}
