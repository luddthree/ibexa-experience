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
use Ibexa\Migration\ValueObject\Step\UserGroupCreateStep;
use Ibexa\Migration\ValueObject\UserGroup\CreateMetadata;
use Webmozart\Assert\Assert;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<
 *     \Ibexa\Migration\ValueObject\Step\UserGroupCreateStep
 * >
 */
final class UserGroupCreateStepNormalizer extends AbstractStepNormalizer
{
    public function getType(): string
    {
        return 'user_group';
    }

    public function getMode(): string
    {
        return Mode::CREATE;
    }

    public function getHandledClassType(): string
    {
        return UserGroupCreateStep::class;
    }

    /**
     * @param array<string, mixed> $context
     * @param \Ibexa\Migration\ValueObject\Step\UserGroupCreateStep $object
     *
     * @return array{
     *     metadata: mixed,
     *     fields?: mixed,
     *     roles?: mixed,
     *     references?: mixed,
     * }
     */
    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        $normalized = [
            'metadata' => $this->normalizer->normalize($object->metadata, $format, $context),
        ];

        if ($object->fields) {
            $normalized['fields'] = $this->normalizer->normalize($object->fields, $format, $context);
        }

        if ($object->roles) {
            $normalized['roles'] = $this->normalizer->normalize($object->roles, $format, $context);
        }

        if ($object->references) {
            $normalized['references'] = $this->normalizer->normalize($object->references, $format, $context);
        }

        return $normalized;
    }

    /**
     * @param array{
     *     metadata: array,
     *     fields?: array,
     * } $data
     * @param array<string, mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\UserGroupCreateStep
     */
    protected function denormalizeStep($data, string $type, string $format = null, array $context = []): StepInterface
    {
        Assert::keyExists($data, 'metadata');
        Assert::isArray($data['metadata']);

        /** @var \Ibexa\Migration\ValueObject\UserGroup\CreateMetadata $metadata */
        $metadata = $this->denormalizer->denormalize($data['metadata'], CreateMetadata::class, $format, $context);

        /** @var \Ibexa\Migration\ValueObject\Content\Field[] $fields */
        $fields = $this->denormalizer->denormalize(
            $data['fields'] ?? [],
            Field::class . '[]',
            $format,
            $context
        );

        /** @var \Ibexa\Migration\Generator\Reference\ReferenceDefinition[] $references */
        $references = $this->denormalizer->denormalize(
            $data['references'] ?? [],
            ReferenceDefinition::class . '[]',
            $format,
            $context
        );

        return new UserGroupCreateStep(
            $metadata,
            $fields,
            $data['roles'] ?? [],
            $references,
        );
    }
}

class_alias(UserGroupCreateStepNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Step\UserGroupCreateStepNormalizer');
