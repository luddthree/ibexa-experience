<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Step\ContentTypeGroup;

use Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\ValueObject\ContentTypeGroup\Matcher;
use Ibexa\Migration\ValueObject\Step\ContentTypeGroupDeleteStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<
 *     \Ibexa\Migration\ValueObject\Step\ContentTypeGroupDeleteStep
 * >
 */
final class ContentTypeGroupDeleteStepNormalizer extends AbstractStepNormalizer
{
    public function getType(): string
    {
        return 'content_type_group';
    }

    public function getMode(): string
    {
        return Mode::DELETE;
    }

    public function getHandledClassType(): string
    {
        return ContentTypeGroupDeleteStep::class;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\ContentTypeGroupDeleteStep $object
     * @param array<string, mixed> $context
     *
     * @return array{
     *     match: mixed,
     * }
     */
    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        return [
            'match' => $this->normalizer->normalize($object->match),
        ];
    }

    /**
     * @param array{
     *     match: array,
     * } $data
     * @param array<string, mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\ContentTypeGroupDeleteStep
     */
    protected function denormalizeStep($data, string $type, string $format = null, array $context = []): StepInterface
    {
        /** @var \Ibexa\Migration\ValueObject\ContentTypeGroup\Matcher */
        $match = $this->denormalizer->denormalize($data['match'], Matcher::class, $format, $context);

        return new ContentTypeGroupDeleteStep($match);
    }
}

class_alias(ContentTypeGroupDeleteStepNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Step\ContentTypeGroup\ContentTypeGroupDeleteStepNormalizer');
