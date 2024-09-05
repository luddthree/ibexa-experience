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
use Ibexa\Segmentation\Value\SegmentMatcher;
use Ibexa\Segmentation\Value\Step\SegmentUpdateStep;
use Webmozart\Assert\Assert;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<
 *     \Ibexa\Segmentation\Value\Step\SegmentUpdateStep
 * >
 */
final class SegmentUpdateStepNormalizer extends AbstractStepNormalizer
{
    public function getType(): string
    {
        return 'segment';
    }

    public function getMode(): string
    {
        return Mode::UPDATE;
    }

    public function getHandledClassType(): string
    {
        return SegmentUpdateStep::class;
    }

    /**
     * @param \Ibexa\Segmentation\Value\Step\SegmentUpdateStep $object
     * @param array<string, mixed> $context
     *
     * @return array{
     *     name?: string|null,
     *     identifier?: string|null,
     *     matcher: mixed,
     * }
     */
    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        $data = [];

        if ($object->getName() !== null) {
            $data['name'] = $object->getName();
        }

        if ($object->getIdentifier() !== null) {
            $data['identifier'] = $object->getIdentifier();
        }

        $data['matcher'] = $this->normalizer->normalize($object->getMatcher(), $format, $context);

        return $data;
    }

    /**
     * @param array{
     *     name?: string|null,
     *     identifier?: string|null,
     *     matcher: \Ibexa\Segmentation\Value\SegmentMatcher,
     * } $data
     * @param array<string, mixed> $context
     */
    protected function denormalizeStep($data, string $type, string $format = null, array $context = []): StepInterface
    {
        Assert::isArray($data);
        $name = $data['name'] ?? null;
        Assert::nullOrString($name);
        $identifier = $data['identifier'] ?? null;
        Assert::nullOrString($identifier);
        Assert::keyExists($data, 'matcher');

        /** @var \Ibexa\Segmentation\Value\SegmentMatcher $matcher */
        $matcher = $this->denormalizer->denormalize($data['matcher'], SegmentMatcher::class, $format, $context);

        return new SegmentUpdateStep(
            $matcher,
            $identifier,
            $name,
        );
    }
}
