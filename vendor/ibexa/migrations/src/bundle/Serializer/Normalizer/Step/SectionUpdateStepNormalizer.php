<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Section\Matcher;
use Ibexa\Migration\ValueObject\Section\UpdateMetadata;
use Ibexa\Migration\ValueObject\Step\SectionUpdateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Webmozart\Assert\Assert;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<
 *     \Ibexa\Migration\ValueObject\Step\SectionUpdateStep
 * >
 */
final class SectionUpdateStepNormalizer extends AbstractStepNormalizer
{
    public function getType(): string
    {
        return 'section';
    }

    public function getMode(): string
    {
        return Mode::UPDATE;
    }

    public function getHandledClassType(): string
    {
        return SectionUpdateStep::class;
    }

    /**
     * @param array<mixed> $context
     * @param \Ibexa\Migration\ValueObject\Step\SectionUpdateStep $object
     *
     * @return array{
     *     match: mixed,
     *     metadata: mixed,
     *     references: mixed
     * }
     */
    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        return [
            'match' => $this->normalizer->normalize($object->match, $format, $context),
            'metadata' => $this->normalizer->normalize($object->metadata, $format, $context),
            'references' => $this->normalizer->normalize($object->references, $format, $context),
        ];
    }

    /**
     * @param array{
     *     metadata: array,
     *     match: array,
     *     references: array
     * } $data
     * @param array<mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\SectionUpdateStep
     */
    protected function denormalizeStep($data, string $type, string $format = null, array $context = []): StepInterface
    {
        Assert::keyExists($data, 'metadata');
        Assert::isArray($data['metadata']);
        Assert::keyExists($data, 'match');
        Assert::isArray($data['match']);

        /** @var \Ibexa\Migration\ValueObject\Section\Matcher $match */
        $match = $this->denormalizer->denormalize($data['match'], Matcher::class, $format, $context);

        /** @var \Ibexa\Migration\ValueObject\Section\UpdateMetadata $metadata */
        $metadata = $this->denormalizer->denormalize($data['metadata'], UpdateMetadata::class, $format, $context);

        /** @var \Ibexa\Migration\Generator\Reference\ReferenceDefinition[] $references */
        $references = $this->denormalizer->denormalize(
            $data['references'] ?? [],
            ReferenceDefinition::class . '[]',
            $format,
            $context
        );

        return new SectionUpdateStep(
            $match,
            $metadata,
            $references
        );
    }
}

class_alias(SectionUpdateStepNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Step\SectionUpdateStepNormalizer');
