<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Serializer;

use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorMapping;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorResolverInterface;

final class AttributeDiscriminatorResolver implements ClassDiscriminatorResolverInterface
{
    private AttributeClassDiscriminatorMappingFactory $mappingFactory;

    public function __construct(AttributeClassDiscriminatorMappingFactory $mappingFactory)
    {
        $this->mappingFactory = $mappingFactory;
    }

    public function getMappingForClass(string $class): ?ClassDiscriminatorMapping
    {
        if (is_a($class, AttributeInterface::class, true)) {
            return $this->mappingFactory->getMapping();
        }

        return null;
    }

    public function getMappingForMappedObject($object): ?ClassDiscriminatorMapping
    {
        if ($object instanceof AttributeInterface) {
            return $this->mappingFactory->getMapping();
        }

        return null;
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\AttributeInterface $object
     */
    public function getTypeForMappedObject($object): ?string
    {
        return $object->getAttributeDefinition()->getType()->getIdentifier();
    }
}
