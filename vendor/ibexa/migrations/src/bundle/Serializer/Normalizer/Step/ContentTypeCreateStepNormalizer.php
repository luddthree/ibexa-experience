<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Service\FieldTypeServiceInterface;
use Ibexa\Migration\ValueObject\ContentType\CreateMetadata;
use Ibexa\Migration\ValueObject\ContentType\FieldDefinitionCollection;
use Ibexa\Migration\ValueObject\Step\ContentTypeCreateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Webmozart\Assert\Assert;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<
 *     \Ibexa\Migration\ValueObject\Step\ContentTypeCreateStep
 * >
 */
class ContentTypeCreateStepNormalizer extends AbstractStepNormalizer
{
    /** @var \Ibexa\Migration\Service\FieldTypeServiceInterface */
    protected $fieldTypeService;

    public function __construct(
        FieldTypeServiceInterface $fieldTypeService
    ) {
        $this->fieldTypeService = $fieldTypeService;
    }

    public function getType(): string
    {
        return 'content_type';
    }

    public function getMode(): string
    {
        return Mode::CREATE;
    }

    public function getHandledClassType(): string
    {
        return ContentTypeCreateStep::class;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\ContentTypeCreateStep $object
     * @param array<string, mixed> $context
     *
     * @return array{
     *     metadata: mixed,
     *     fields: mixed,
     *     references: mixed,
     * }
     */
    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        return [
            'metadata' => $this->normalizer->normalize($object->getMetadata(), $format, $context),
            'fields' => $this->normalizer->normalize($object->getFields(), $format, $context),
            'references' => $this->normalizer->normalize($object->getReferences(), $format, $context),
        ];
    }

    /**
     * @param array{
     *     metadata: array,
     *     fields: array,
     *     references: array,
     * } $data
     * @param array<string, mixed> $context
     */
    protected function denormalizeStep($data, string $type, string $format = null, array $context = []): StepInterface
    {
        Assert::keyExists($data, 'metadata');
        Assert::isArray($data['metadata']);

        Assert::keyExists($data, 'fields');
        Assert::isArray($data['fields']);

        /** @var \Ibexa\Migration\ValueObject\ContentType\CreateMetadata $metadata */
        $metadata = $this->denormalizer->denormalize(
            $data['metadata'],
            CreateMetadata::class,
            $format,
            $context
        );

        Assert::isInstanceOf($metadata, CreateMetadata::class);

        /** @var \Ibexa\Migration\ValueObject\ContentType\FieldDefinitionCollection $fieldsDefinitionCollection */
        $fieldsDefinitionCollection = $this->denormalizer->denormalize(
            $data['fields'],
            FieldDefinitionCollection::class,
            $format,
            $context
        );

        Assert::isInstanceOf($fieldsDefinitionCollection, FieldDefinitionCollection::class);

        return new ContentTypeCreateStep(
            $metadata,
            $fieldsDefinitionCollection
        );
    }
}

class_alias(ContentTypeCreateStepNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Step\ContentTypeCreateStepNormalizer');
