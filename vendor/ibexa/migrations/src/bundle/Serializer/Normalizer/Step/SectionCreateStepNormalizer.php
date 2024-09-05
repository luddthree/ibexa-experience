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
use Ibexa\Migration\ValueObject\Section\CreateMetadata;
use Ibexa\Migration\ValueObject\Step\SectionCreateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Webmozart\Assert\Assert;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<
 *     \Ibexa\Migration\ValueObject\Step\SectionCreateStep
 * >
 */
final class SectionCreateStepNormalizer extends AbstractStepNormalizer
{
    public function getType(): string
    {
        return 'section';
    }

    public function getMode(): string
    {
        return Mode::CREATE;
    }

    public function getHandledClassType(): string
    {
        return SectionCreateStep::class;
    }

    /**
     * @param array<mixed> $context
     * @param \Ibexa\Migration\ValueObject\Step\SectionCreateStep $object
     *
     * @return array{
     *     metadata: mixed,
     *     references: mixed
     * }
     */
    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        return [
            'metadata' => $this->normalizer->normalize($object->metadata, $format, $context),
            'references' => $this->normalizer->normalize($object->references, $format, $context),
        ];
    }

    /**
     * @param array{
     *     metadata: array,
     *     references: array
     * } $data
     * @param array<mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\SectionCreateStep
     */
    protected function denormalizeStep($data, string $type, string $format = null, array $context = []): StepInterface
    {
        Assert::keyExists($data, 'metadata');
        Assert::isArray($data['metadata']);

        /** @var \Ibexa\Migration\ValueObject\Section\CreateMetadata $metadata */
        $metadata = $this->denormalizer->denormalize($data['metadata'], CreateMetadata::class, $format, $context);

        /** @var \Ibexa\Migration\Generator\Reference\ReferenceDefinition[] $references */
        $references = $this->denormalizer->denormalize(
            $data['references'] ?? [],
            ReferenceDefinition::class . '[]',
            $format,
            $context
        );

        return new SectionCreateStep(
            $metadata,
            $references
        );
    }
}

class_alias(SectionCreateStepNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Step\SectionCreateStepNormalizer');
