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
 *     \Ibexa\ProductCatalog\Migrations\Attribute\AttributeUpdateStep
 * >
 */
final class AttributeUpdateStepNormalizer extends AbstractStepNormalizer
{
    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        assert($object instanceof AttributeUpdateStep);

        return [
            'criteria' => $this->normalizer->normalize($object->getCriterion(), $format, $context),
            'identifier' => $object->getIdentifier(),
            'attribute_group_identifier' => $object->getAttributeGroupIdentifier(),
            'position' => $object->getPosition(),
            'names' => $object->getNames(),
            'descriptions' => $object->getDescriptions(),
            'options' => $object->getOptions(),
        ];
    }

    protected function denormalizeStep($data, string $type, string $format, array $context = []): AttributeUpdateStep
    {
        Assert::keyExists($data, 'criteria');

        if (isset($data['identifier'])) {
            Assert::stringNotEmpty($data['identifier']);
        }

        if (isset($data['attribute_group_identifier'])) {
            Assert::stringNotEmpty($data['attribute_group_identifier']);
        }

        if (isset($data['names'])) {
            Assert::isArray($data['names']);
            Assert::allString(
                array_keys($data['names']),
                'Expected a string key for Attribute names property. Got: %s',
            );
            Assert::allString(
                $data['names'],
                'Expected a string as Attribute name. Got: %s',
            );
        }

        $descriptions = $data['descriptions'] ?? [];
        Assert::isArray($descriptions);

        $options = $data['options'] ?? [];
        Assert::isArray($options);

        return new AttributeUpdateStep(
            $this->denormalizer->denormalize($data['criteria'], CriterionInterface::class, $format, $context),
            $data['identifier'] ?? null,
            $data['attribute_group_identifier'] ?? null,
            $data['position'] ?? null,
            $data['names'] ?? [],
            $descriptions,
            $options,
        );
    }

    public function getHandledClassType(): string
    {
        return AttributeUpdateStep::class;
    }

    public function getType(): string
    {
        return 'attribute';
    }

    public function getMode(): string
    {
        return 'update';
    }
}
