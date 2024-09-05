<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\Attribute;

use Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Webmozart\Assert\Assert;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<
 *     \Ibexa\ProductCatalog\Migrations\Attribute\AttributeCreateStep
 * >
 */
final class AttributeCreateStepNormalizer extends AbstractStepNormalizer
{
    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        assert($object instanceof AttributeCreateStep);

        return [
            'identifier' => $object->getIdentifier(),
            'attribute_group_identifier' => $object->getAttributeGroupIdentifier(),
            'attribute_type_identifier' => $object->getAttributeTypeIdentifier(),
            'position' => $object->getPosition(),
            'names' => $object->getNames(),
            'descriptions' => $object->getDescriptions(),
            'options' => $object->getOptions(),
        ];
    }

    protected function denormalizeStep($data, string $type, string $format, array $context = []): AttributeCreateStep
    {
        Assert::keyExists($data, 'identifier');
        Assert::stringNotEmpty($data['identifier']);
        Assert::keyExists($data, 'attribute_group_identifier');
        Assert::stringNotEmpty($data['attribute_group_identifier']);
        Assert::keyExists($data, 'attribute_type_identifier');
        Assert::stringNotEmpty($data['attribute_type_identifier']);

        Assert::keyExists($data, 'names');
        Assert::isArray($data['names']);
        Assert::allString(
            array_keys($data['names']),
            'Expected a string key for Attribute names property. Got: %s',
        );
        Assert::allString(
            $data['names'],
            'Expected a string as Attribute name. Got: %s',
        );

        $descriptions = $data['descriptions'] ?? [];
        Assert::isArray($descriptions);

        $options = $data['options'] ?? [];
        Assert::isArray($options);

        return new AttributeCreateStep(
            $data['identifier'],
            $data['attribute_group_identifier'],
            $data['attribute_type_identifier'],
            $data['position'] ?? 0,
            $data['names'],
            $descriptions,
            $options,
        );
    }

    public function getHandledClassType(): string
    {
        return AttributeCreateStep::class;
    }

    public function getType(): string
    {
        return 'attribute';
    }

    public function getMode(): string
    {
        return 'create';
    }
}
