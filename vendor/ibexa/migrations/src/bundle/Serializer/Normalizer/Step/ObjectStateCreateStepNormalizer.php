<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\ValueObject\ObjectState\CreateMetadata;
use Ibexa\Migration\ValueObject\Step\ObjectStateCreateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Webmozart\Assert\Assert;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<
 *     \Ibexa\Migration\ValueObject\Step\ObjectStateCreateStep
 * >
 */
final class ObjectStateCreateStepNormalizer extends AbstractStepNormalizer
{
    public function getType(): string
    {
        return 'object_state';
    }

    public function getMode(): string
    {
        return Mode::CREATE;
    }

    public function getHandledClassType(): string
    {
        return ObjectStateCreateStep::class;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\ObjectStateCreateStep $object
     * @param array<mixed> $context
     *
     * @return array{
     *     metadata: mixed,
     * }
     */
    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        return [
            'metadata' => $this->normalizer->normalize($object->metadata, $format, $context),
        ];
    }

    /**
     * @param array{
     *     metadata: array,
     * } $data
     * @param array<mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\ObjectStateCreateStep
     */
    protected function denormalizeStep($data, string $type, string $format = null, array $context = []): StepInterface
    {
        Assert::keyExists($data, 'metadata');
        Assert::isArray($data['metadata']);

        /** @var \Ibexa\Migration\ValueObject\ObjectState\CreateMetadata $metadata */
        $metadata = $this->denormalizer->denormalize($data['metadata'], CreateMetadata::class, $format, $context);

        return new ObjectStateCreateStep(
            $metadata
        );
    }
}

class_alias(ObjectStateCreateStepNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Step\ObjectStateCreateStepNormalizer');
