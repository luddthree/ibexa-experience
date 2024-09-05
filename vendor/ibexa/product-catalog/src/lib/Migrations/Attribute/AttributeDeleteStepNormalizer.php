<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\Attribute;

use Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Webmozart\Assert\Assert;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<
 *     \Ibexa\ProductCatalog\Migrations\Attribute\AttributeDeleteStep
 * >
 */
final class AttributeDeleteStepNormalizer extends AbstractStepNormalizer
{
    protected function normalizeStep(
        StepInterface $object,
        string $format = null,
        array $context = []
    ): array {
        assert($object instanceof AttributeDeleteStep);

        return [
            'criteria' => $this->normalizer->normalize($object->getCriterion(), $format, $context),
        ];
    }

    protected function denormalizeStep(
        $data,
        string $type,
        string $format,
        array $context = []
    ): AttributeDeleteStep {
        Assert::keyExists($data, 'criteria');

        return new AttributeDeleteStep(
            $this->denormalizer->denormalize($data['criteria'], CriterionInterface::class, $format, $context),
        );
    }

    public function getHandledClassType(): string
    {
        return AttributeDeleteStep::class;
    }

    public function getType(): string
    {
        return 'attribute';
    }

    public function getMode(): string
    {
        return 'delete';
    }
}
