<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Segmentation\Serializer\Normalizer\Step;

use Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Segmentation\Value\Step\SegmentGroupCreateStep;
use Webmozart\Assert\Assert;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<
 *     \Ibexa\Segmentation\Value\Step\SegmentGroupCreateStep
 * >
 */
final class SegmentGroupCreateStepNormalizer extends AbstractStepNormalizer
{
    public function getType(): string
    {
        return 'segment_group';
    }

    public function getMode(): string
    {
        return Mode::CREATE;
    }

    public function getHandledClassType(): string
    {
        return SegmentGroupCreateStep::class;
    }

    /**
     * @param \Ibexa\Segmentation\Value\Step\SegmentGroupCreateStep $object
     * @param array<string, mixed> $context
     *
     * @return array{
     *     name: mixed,
     *     identifier: mixed,
     *     references: mixed,
     * }
     */
    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        return [
            'name' => $object->getName(),
            'identifier' => $object->getIdentifier(),
            'references' => $this->normalizer->normalize($object->getReferences(), $format, $context),
        ];
    }

    /**
     * @param array{
     *     identifier: string,
     *     name: string,
     *     references?: mixed,
     * } $data
     * @param array<string, mixed> $context
     */
    protected function denormalizeStep($data, string $type, string $format = null, array $context = []): StepInterface
    {
        Assert::isArray($data);
        Assert::string($data['identifier']);
        Assert::string($data['name']);

        /** @var \Ibexa\Migration\Generator\Reference\ReferenceDefinition[] $references */
        $references = $this->denormalizer->denormalize($data['references'] ?? [], ReferenceDefinition::class . '[]', $format, $context);

        return new SegmentGroupCreateStep(
            $data['identifier'],
            $data['name'],
            $references
        );
    }
}
