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
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Migration\ValueObject\Step\UserUpdateStep;
use Ibexa\Migration\ValueObject\User\UpdateMetadata;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<
 *     \Ibexa\Migration\ValueObject\Step\UserUpdateStep
 * >
 */
final class UserUpdateStepNormalizer extends AbstractStepNormalizer
{
    public function getType(): string
    {
        return 'user';
    }

    public function getMode(): string
    {
        return Mode::UPDATE;
    }

    public function getHandledClassType(): string
    {
        return UserUpdateStep::class;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\UserUpdateStep $object
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
                AbstractContextAwareCriterionNormalizer::CRITERION_CONTEXT_KEY => 'user',
            ] + $context),
            'metadata' => $this->normalizer->normalize($object->metadata, $format, $context),
            'fields' => $this->normalizer->normalize($object->fields, $format, $context),
        ];
    }

    /**
     * @param array{
     *     match: array,
     *     metadata?: array,
     *     fields?: array,
     * } $data
     * @param array<string, mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\UserUpdateStep
     */
    protected function denormalizeStep($data, string $type, string $format = null, array $context = []): StepInterface
    {
        /** @var \Ibexa\Migration\ValueObject\User\UpdateMetadata $metadata */
        $metadata = $this->denormalizer->denormalize(
            $data['metadata'] ?? [],
            UpdateMetadata::class,
            $format,
            $context
        );

        /** @var \Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion $match */
        $match = $this->denormalizer->denormalize($data['match'], FilteringCriterion::class, $format, [
            AbstractContextAwareCriterionNormalizer::CRITERION_CONTEXT_KEY => 'user',
        ] + $context);

        /** @var \Ibexa\Migration\ValueObject\Content\Field[] $fields */
        $fields = $this->denormalizer->denormalize(
            $data['fields'] ?? [],
            Field::class . '[]',
            $format,
            $context
        );

        return new UserUpdateStep(
            $metadata,
            $match,
            $fields
        );
    }
}

class_alias(UserUpdateStepNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Step\UserUpdateStepNormalizer');
