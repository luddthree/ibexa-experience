<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Step\ContentTypeGroup;

use Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\ValueObject\ContentTypeGroup\CreateMetadata;
use Ibexa\Migration\ValueObject\Step\ContentTypeGroupCreateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Webmozart\Assert\Assert;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<
 *     \Ibexa\Migration\ValueObject\Step\ContentTypeGroupCreateStep
 * >
 */
final class ContentTypeGroupCreateStepNormalizer extends AbstractStepNormalizer
{
    public function getType(): string
    {
        return 'content_type_group';
    }

    public function getMode(): string
    {
        return Mode::CREATE;
    }

    public function getHandledClassType(): string
    {
        return ContentTypeGroupCreateStep::class;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\ContentTypeGroupCreateStep $object
     * @param array<string, mixed> $context
     *
     * @return array{
     *     metadata: mixed,
     *     references: mixed,
     * }
     */
    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        return [
            'metadata' => $this->normalizer->normalize($object->metadata),
            'references' => $this->normalizer->normalize($object->references),
        ];
    }

    /**
     * @param array{
     *     metadata: array,
     *     references: array,
     * } $data
     * @param array<string, mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\ContentTypeGroupCreateStep
     */
    protected function denormalizeStep($data, string $type, string $format = null, array $context = []): StepInterface
    {
        Assert::keyExists($data, 'metadata');
        Assert::isArray($data['metadata']);

        /** @var \Ibexa\Migration\ValueObject\ContentTypeGroup\CreateMetadata $metadata */
        $metadata = $this->denormalizer->denormalize(
            $data['metadata'],
            CreateMetadata::class,
            $format,
            $context
        );

        Assert::isInstanceOf($metadata, CreateMetadata::class);

        return new ContentTypeGroupCreateStep(
            $metadata
        );
    }
}

class_alias(ContentTypeGroupCreateStepNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Step\ContentTypeGroup\ContentTypeGroupCreateStepNormalizer');
