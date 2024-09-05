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
use Ibexa\Migration\ValueObject\ContentTypeGroup\UpdateMetadata;
use Ibexa\Migration\ValueObject\Step\ContentTypeGroupUpdateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Webmozart\Assert\Assert;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<
 *     \Ibexa\Migration\ValueObject\Step\ContentTypeGroupUpdateStep
 * >
 */
final class ContentTypeGroupUpdateStepNormalizer extends AbstractStepNormalizer
{
    public function getType(): string
    {
        return 'content_type_group';
    }

    public function getMode(): string
    {
        return Mode::UPDATE;
    }

    public function getHandledClassType(): string
    {
        return ContentTypeGroupUpdateStep::class;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\ContentTypeGroupUpdateStep $object
     * @param array<string, mixed> $context
     *
     * @return array{
     *     match: mixed,
     *     metadata: mixed,
     * }
     */
    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        return [
            'match' => $this->normalizer->normalize($object->match),
            'metadata' => $this->normalizer->normalize($object->metadata),
        ];
    }

    /**
     * @param array{
     *     match: array,
     *     metadata: array,
     * } $data
     * @param array<string, mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\ContentTypeGroupUpdateStep
     */
    protected function denormalizeStep($data, string $type, string $format = null, array $context = []): StepInterface
    {
        Assert::keyExists($data, 'metadata');
        Assert::isArray($data['metadata']);

        /** @var \Ibexa\Migration\ValueObject\ContentTypeGroup\UpdateMetadata $metadata */
        $metadata = $this->denormalizer->denormalize($data['metadata'], UpdateMetadata::class, $format, $context);

        /** @var \Ibexa\Migration\ValueObject\ContentTypeGroup\Matcher $match */
        $match = $this->denormalizer->denormalize($data['match'], Matcher::class, $format, $context);

        return new ContentTypeGroupUpdateStep(
            $metadata,
            $match
        );
    }
}

class_alias(ContentTypeGroupUpdateStepNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Step\ContentTypeGroup\ContentTypeGroupUpdateStepNormalizer');
