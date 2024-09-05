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
use Ibexa\Migration\ValueObject\Step\ContentDeleteStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<
 *     \Ibexa\Migration\ValueObject\Step\ContentDeleteStep
 * >
 */
final class ContentDeleteStepNormalizer extends AbstractStepNormalizer
{
    public function getType(): string
    {
        return 'content';
    }

    public function getMode(): string
    {
        return Mode::DELETE;
    }

    public function getHandledClassType(): string
    {
        return ContentDeleteStep::class;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\ContentDeleteStep $object
     * @param array<string, mixed> $context
     *
     * @return array{
     *     match: mixed,
     * }
     */
    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        return [
            'match' => $this->normalizer->normalize($object->criterion, $format, [
                AbstractContextAwareCriterionNormalizer::CRITERION_CONTEXT_KEY => 'content',
            ] + $context),
        ];
    }

    /**
     * @param array{
     *     match: mixed,
     * } $data
     * @param array<string, mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\ContentDeleteStep
     */
    protected function denormalizeStep($data, string $type, string $format = null, array $context = []): StepInterface
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion $criterion */
        $criterion = $this->denormalizer->denormalize($data['match'], FilteringCriterion::class, $format, [
            AbstractContextAwareCriterionNormalizer::CRITERION_CONTEXT_KEY => 'content',
        ] + $context);

        return new ContentDeleteStep($criterion);
    }
}

class_alias(ContentDeleteStepNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Step\ContentDeleteStepNormalizer');
