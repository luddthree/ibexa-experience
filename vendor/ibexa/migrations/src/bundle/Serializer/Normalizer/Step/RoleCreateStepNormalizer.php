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
use Ibexa\Migration\ValueObject\Step\Role\CreateMetadata;
use Ibexa\Migration\ValueObject\Step\Role\Policy;
use Ibexa\Migration\ValueObject\Step\RoleCreateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Webmozart\Assert\Assert;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<
 *     \Ibexa\Migration\ValueObject\Step\RoleCreateStep
 * >
 */
final class RoleCreateStepNormalizer extends AbstractStepNormalizer
{
    public function getType(): string
    {
        return 'role';
    }

    public function getMode(): string
    {
        return Mode::CREATE;
    }

    public function getHandledClassType(): string
    {
        return RoleCreateStep::class;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\RoleCreateStep $roleCreateStep
     * @param array<string, mixed> $context
     *
     * @return array{
     *     metadata: mixed,
     *     policies: mixed,
     *     references: mixed,
     * }
     */
    protected function normalizeStep(StepInterface $roleCreateStep, string $format = null, array $context = []): array
    {
        Assert::isInstanceOf($roleCreateStep, RoleCreateStep::class);

        return [
            'metadata' => $this->normalizer->normalize($roleCreateStep->metadata, $format, $context),
            'policies' => $this->normalizer->normalize($roleCreateStep->policies, $format, $context),
            'references' => $this->normalizer->normalize($roleCreateStep->references, $format, $context),
        ];
    }

    /**
     * @param array{
     *     metadata: array,
     *     policies: array,
     *     references: array,
     * } $data
     * @param array<string, mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\RoleCreateStep
     */
    protected function denormalizeStep($data, string $type, string $format = null, array $context = []): StepInterface
    {
        $metadata = $this->denormalizer->denormalize($data['metadata'], CreateMetadata::class, $format, $context);
        /** @var \Ibexa\Migration\ValueObject\Step\Role\CreateMetadata $metadata */
        Assert::isInstanceOf($metadata, CreateMetadata::class);

        $policies = $this->denormalizer->denormalize(
            $data['policies'] ?? [],
            Policy::class . '[]',
            $format,
            $context
        );
        Assert::allIsInstanceOf($policies, Policy::class);

        /** @var \Ibexa\Migration\Generator\Reference\ReferenceDefinition[] $references */
        $references = $this->denormalizer->denormalize($data['references'] ?? [], ReferenceDefinition::class . '[]', $format, $context);

        return new RoleCreateStep(
            $metadata,
            $policies,
            $references,
        );
    }
}

class_alias(RoleCreateStepNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Step\RoleCreateStepNormalizer');
