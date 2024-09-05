<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\AttributeGroup;

use Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Webmozart\Assert\Assert;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<
 *     \Ibexa\ProductCatalog\Migrations\AttributeGroup\AttributeGroupUpdateStep
 * >
 */
final class AttributeGroupUpdateStepNormalizer extends AbstractStepNormalizer
{
    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        assert($object instanceof AttributeGroupUpdateStep);

        return [
            'criteria' => $this->normalizer->normalize($object->getCriterion(), $format, $context),
            'identifier' => $object->getIdentifier(),
            'position' => $object->getPosition(),
            'names' => $object->getNames(),
        ];
    }

    protected function denormalizeStep(
        $data,
        string $type,
        string $format,
        array $context = []
    ): AttributeGroupUpdateStep {
        Assert::keyExists($data, 'criteria');

        $criterion = $this->denormalizer->denormalize($data['criteria'], CriterionInterface::class, $format, $context);

        $identifier = $data['identifier'] ?? null;
        if ($identifier !== null) {
            Assert::stringNotEmpty($identifier);
        }

        $names = $data['names'] ?? [];
        Assert::isArray($names);
        Assert::allStringNotEmpty(
            array_keys($names),
            'Expected a string key for Attribute Group names property. Got: %s',
        );
        Assert::allStringNotEmpty(
            $names,
            'Expected a string as Attribute Group name. Got: %s',
        );

        $position = $data['position'] ?? null;
        Assert::nullOrInteger($position);

        return new AttributeGroupUpdateStep($criterion, $identifier, $names, $position);
    }

    public function getHandledClassType(): string
    {
        return AttributeGroupUpdateStep::class;
    }

    public function getType(): string
    {
        return 'attribute_group';
    }

    public function getMode(): string
    {
        return 'update';
    }
}
