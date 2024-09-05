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
use Ibexa\Migration\ValueObject\Step\Role\Matcher;
use Ibexa\Migration\ValueObject\Step\Role\PolicyList;
use Ibexa\Migration\ValueObject\Step\Role\UpdateMetadata;
use Ibexa\Migration\ValueObject\Step\RoleUpdateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Webmozart\Assert\Assert;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<
 *     \Ibexa\Migration\ValueObject\Step\RoleUpdateStep
 * >
 */
final class RoleUpdateStepNormalizer extends AbstractStepNormalizer
{
    public function getType(): string
    {
        return 'role';
    }

    public function getMode(): string
    {
        return Mode::UPDATE;
    }

    public function getHandledClassType(): string
    {
        return RoleUpdateStep::class;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\RoleUpdateStep $object
     * @param array<string, mixed> $context
     *
     * @return array{
     *     match: mixed,
     *     metadata: mixed,
     *     policies: mixed,
     *     references: mixed,
     * }
     */
    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        Assert::isInstanceOf($object, RoleUpdateStep::class);

        return [
            'match' => $this->normalizer->normalize($object->match, $format, $context),
            'metadata' => $this->normalizer->normalize($object->metadata, $format, $context),
            'policies' => $this->normalizer->normalize($object->getPolicyList(), $format, $context),
            'references' => $this->normalizer->normalize($object->references, $format, $context),
        ];
    }

    /**
     * @param array{
     *     match: array,
     *     metadata: array,
     *     policies: array,
     *     references: array,
     * } $data
     * @param array<string, mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\RoleUpdateStep
     */
    protected function denormalizeStep($data, string $type, string $format = null, array $context = []): StepInterface
    {
        /** @var \Ibexa\Migration\ValueObject\Step\Role\Matcher $match */
        $match = $this->denormalizer->denormalize($data['match'], Matcher::class, $format, $context);

        /** @var \Ibexa\Migration\ValueObject\Step\Role\UpdateMetadata $metadata */
        $metadata = $this->denormalizer->denormalize($data['metadata'] ?? [], UpdateMetadata::class, $format, $context);

        $policyList = null;
        if (isset($data['policies'])) {
            /** @var \Ibexa\Migration\ValueObject\Step\Role\PolicyList $policyList */
            $policyList = $this->denormalizer->denormalize($data['policies'], PolicyList::class, $format, $context);
        }

        /** @var \Ibexa\Migration\Generator\Reference\ReferenceDefinition[] $references */
        $references = $this->denormalizer->denormalize($data['references'] ?? [], ReferenceDefinition::class . '[]', $format, $context);

        return new RoleUpdateStep(
            $match,
            $metadata,
            $policyList,
            $references,
        );
    }
}

class_alias(RoleUpdateStepNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Step\RoleUpdateStepNormalizer');
