<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\AttributeGroup;

use Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Webmozart\Assert\Assert;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<
 *     \Ibexa\ProductCatalog\Migrations\AttributeGroup\AttributeGroupCreateStep
 * >
 */
final class AttributeGroupCreateStepNormalizer extends AbstractStepNormalizer
{
    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        assert($object instanceof AttributeGroupCreateStep);

        return [
            'identifier' => $object->getIdentifier(),
            'position' => $object->getPosition(),
            'names' => $object->getNames(),
        ];
    }

    protected function denormalizeStep($data, string $type, string $format, array $context = []): AttributeGroupCreateStep
    {
        Assert::keyExists($data, 'identifier');
        Assert::stringNotEmpty($data['identifier']);
        Assert::keyExists($data, 'names');
        Assert::isArray($data['names']);
        Assert::allStringNotEmpty(
            array_keys($data['names']),
            'Expected a string key for Attribute Group names property. Got: %s',
        );
        Assert::allStringNotEmpty(
            $data['names'],
            'Expected a string as Attribute Group name. Got: %s',
        );

        return new AttributeGroupCreateStep($data['identifier'], $data['names'], $data['position'] ?? 0);
    }

    public function getHandledClassType(): string
    {
        return AttributeGroupCreateStep::class;
    }

    public function getType(): string
    {
        return 'attribute_group';
    }

    public function getMode(): string
    {
        return 'create';
    }
}
