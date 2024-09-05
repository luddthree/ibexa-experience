<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Bundle\Migration\Serializer\Normalizer\Criterion\AbstractContextAwareCriterionNormalizer;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;
use Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\ValueObject\Content\Field;
use Ibexa\Migration\ValueObject\Content\UpdateMetadata;
use Ibexa\Migration\ValueObject\Step\ContentUpdateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<
 *     \Ibexa\Migration\ValueObject\Step\ContentUpdateStep
 * >
 */
final class ContentUpdateStepNormalizer extends AbstractStepNormalizer
{
    public function getType(): string
    {
        return 'content';
    }

    public function getMode(): string
    {
        return Mode::UPDATE;
    }

    public function getHandledClassType(): string
    {
        return ContentUpdateStep::class;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\ContentUpdateStep $object
     * @param array<string, mixed> $context
     *
     * @return array{
     *     match: mixed,
     *     metadata: mixed,
     *     fields: mixed,
     * }
     */
    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        return [
            'match' => $this->normalizer->normalize($object->criterion, $format, [
                AbstractContextAwareCriterionNormalizer::CRITERION_CONTEXT_KEY => 'content',
            ] + $context),
            'metadata' => $this->normalizer->normalize($object->metadata, $format, $context),
            'fields' => $this->normalizer->normalize($object->fields, $format, $context),
        ];
    }

    /**
     * @param array{
     *     match: array,
     *     metadata: array,
     *     fields: array,
     *     actions?: array,
     * } $data
     * @param array<string, mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\ContentUpdateStep
     */
    protected function denormalizeStep($data, string $type, string $format = null, array $context = []): StepInterface
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion $criterion */
        $criterion = $this->denormalizer->denormalize($data['match'], FilteringCriterion::class, $format, [
            AbstractContextAwareCriterionNormalizer::CRITERION_CONTEXT_KEY => 'content',
        ] + $context);

        /** @var \Ibexa\Migration\ValueObject\Content\Field[] $fields */
        $fields = $this->denormalizer->denormalize($data['fields'] ?? [], Field::class . '[]', $format, $context);

        return new ContentUpdateStep(
            UpdateMetadata::createFromArray($data['metadata'] ?? []),
            $criterion,
            $fields,
        );
    }
}

class_alias(ContentUpdateStepNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Step\ContentUpdateStepNormalizer');
