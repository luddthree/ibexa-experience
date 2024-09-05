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
use Ibexa\Migration\ValueObject\Content\CreateMetadata;
use Ibexa\Migration\ValueObject\Content\Field;
use Ibexa\Migration\ValueObject\Content\Location;
use Ibexa\Migration\ValueObject\Step\ContentCreateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<
 *     \Ibexa\Migration\ValueObject\Step\ContentCreateStep
 * >
 */
final class ContentCreateStepNormalizer extends AbstractStepNormalizer
{
    public function getType(): string
    {
        return 'content';
    }

    public function getMode(): string
    {
        return Mode::CREATE;
    }

    public function getHandledClassType(): string
    {
        return ContentCreateStep::class;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\ContentCreateStep $object
     * @param array<string, mixed> $context
     *
     * @return array{
     *     metadata: mixed,
     *     location: mixed,
     *     fields: mixed,
     *     references: mixed,
     * }
     */
    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        return [
            'metadata' => $this->normalizer->normalize($object->metadata, $format, $context),
            'location' => $this->normalizer->normalize($object->location, $format, $context),
            'fields' => $this->normalizer->normalize($object->fields, $format, $context),
            'references' => $this->normalizer->normalize($object->references, $format, $context),
        ];
    }

    /**
     * @param array{
     *     metadata: array,
     *     location: array,
     *     fields: array,
     *     references: array,
     * } $data
     * @param array<string, mixed> $context
     */
    protected function denormalizeStep($data, string $type, string $format = null, array $context = []): StepInterface
    {
        /** @var \Ibexa\Migration\ValueObject\Content\CreateMetadata $metadata */
        $metadata = $this->denormalizer->denormalize($data['metadata'], CreateMetadata::class, $format, $context);

        /** @var \Ibexa\Migration\ValueObject\Content\Location $location */
        $location = $this->denormalizer->denormalize($data['location'], Location::class, $format, $context);

        /** @var \Ibexa\Migration\ValueObject\Content\Field[] $fields */
        $fields = $this->denormalizer->denormalize($data['fields'], Field::class . '[]', $format, $context);

        /** @var \Ibexa\Migration\Generator\Reference\ReferenceDefinition[] $references */
        $references = $this->denormalizer->denormalize($data['references'] ?? [], ReferenceDefinition::class . '[]', $format, $context);

        return new ContentCreateStep(
            $metadata,
            $location,
            $fields,
            $references
        );
    }
}

class_alias(ContentCreateStepNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Step\ContentCreateStepNormalizer');
