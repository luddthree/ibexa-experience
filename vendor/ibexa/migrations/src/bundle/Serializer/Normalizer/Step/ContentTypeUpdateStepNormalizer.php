<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\ValueObject\ContentType\FieldDefinitionCollection;
use Ibexa\Migration\ValueObject\ContentType\Matcher;
use Ibexa\Migration\ValueObject\ContentType\UpdateMetadata;
use Ibexa\Migration\ValueObject\Step\ContentTypeUpdateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Webmozart\Assert\Assert;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<
 *     \Ibexa\Migration\ValueObject\Step\ContentTypeUpdateStep
 * >
 */
final class ContentTypeUpdateStepNormalizer extends AbstractStepNormalizer
{
    public function getType(): string
    {
        return 'content_type';
    }

    public function getMode(): string
    {
        return Mode::UPDATE;
    }

    public function getHandledClassType(): string
    {
        return ContentTypeUpdateStep::class;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\ContentTypeUpdateStep $object
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
            'match' => $this->normalizer->normalize($object->getMatch(), $format, $context),
            'metadata' => $this->normalizer->normalize($object->getMetadata(), $format, $context),
            'fields' => $this->normalizer->normalize($object->getFields(), $format, $context),
        ];
    }

    /**
     * @param array{
     *     match: array,
     *     metadata: array,
     *     fields: array,
     * } $data
     * @param array<string, mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\ContentTypeUpdateStep
     */
    protected function denormalizeStep($data, string $type, string $format = null, array $context = []): StepInterface
    {
        $data['metadata'] = $data['metadata'] ?? [];
        $data['fields'] = $data['fields'] ?? [];

        Assert::isArray($data['metadata']);
        Assert::isArray($data['fields']);

        Assert::keyExists($data, 'match');
        Assert::isArray($data['match']);

        $metadata = UpdateMetadata::createFromArray($data['metadata']);

        /** @var \Ibexa\Migration\ValueObject\ContentType\FieldDefinitionCollection $fieldsDefinitionCollection */
        $fieldsDefinitionCollection = $this->denormalizer->denormalize(
            $data['fields'],
            FieldDefinitionCollection::class,
            $format,
            $context
        );

        Assert::isInstanceOf($fieldsDefinitionCollection, FieldDefinitionCollection::class);

        /** @var \Ibexa\Migration\ValueObject\ContentType\Matcher $match */
        $match = $this->denormalizer->denormalize(
            $data['match'],
            Matcher::class,
            $format,
            $context
        );

        return new ContentTypeUpdateStep(
            $metadata,
            $fieldsDefinitionCollection,
            $match,
        );
    }
}

class_alias(ContentTypeUpdateStepNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Step\ContentTypeUpdateStepNormalizer');
