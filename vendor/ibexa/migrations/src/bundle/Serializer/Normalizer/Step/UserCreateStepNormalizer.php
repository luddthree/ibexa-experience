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
use Ibexa\Migration\ValueObject\Content\Field;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Migration\ValueObject\Step\UserCreateStep;
use Ibexa\Migration\ValueObject\User\CreateMetadata;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<
 *     \Ibexa\Migration\ValueObject\Step\UserCreateStep
 * >
 */
final class UserCreateStepNormalizer extends AbstractStepNormalizer
{
    public function getType(): string
    {
        return 'user';
    }

    public function getMode(): string
    {
        return Mode::CREATE;
    }

    public function getHandledClassType(): string
    {
        return UserCreateStep::class;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\UserCreateStep $object
     * @param array<string, mixed> $context
     *
     * @return array{
     *     metadata: mixed,
     *     groups: mixed,
     *     fields: mixed,
     * }
     */
    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        return [
            'metadata' => $this->normalizer->normalize($object->metadata, $format, $context),
            'groups' => $this->normalizer->normalize($object->groups, $format, $context),
            'fields' => $this->normalizer->normalize($object->fields, $format, $context),
            'references' => $this->normalizer->normalize($object->references, $format, $context),
        ];
    }

    /**
     * @param array{
     *     metadata: array,
     *     groups: array,
     *     fields: array,
     * } $data
     * @param array<string, mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\UserCreateStep
     */
    protected function denormalizeStep($data, string $type, string $format = null, array $context = []): StepInterface
    {
        /** @var \Ibexa\Migration\ValueObject\User\CreateMetadata $metadata */
        $metadata = $this->denormalizer->denormalize($data['metadata'], CreateMetadata::class, $format, $context);

        /** @var \Ibexa\Migration\ValueObject\Content\Field[] $fields */
        $fields = $this->denormalizer->denormalize(
            $data['fields'],
            Field::class . '[]',
            $format,
            array_merge($context, [
                'mainLanguage' => $metadata->mainLanguage,
            ])
        );

        /** @var \Ibexa\Migration\Generator\Reference\ReferenceDefinition[] $references */
        $references = $this->denormalizer->denormalize(
            $data['references'] ?? [],
            ReferenceDefinition::class . '[]',
            $format,
            $context
        );

        return new UserCreateStep(
            $metadata,
            $data['groups'],
            $fields,
            $references
        );
    }
}

class_alias(UserCreateStepNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Step\UserCreateStepNormalizer');
