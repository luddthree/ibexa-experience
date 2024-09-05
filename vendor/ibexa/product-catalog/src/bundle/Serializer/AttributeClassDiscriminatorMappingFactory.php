<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Serializer;

use Symfony\Component\Serializer\Mapping\ClassDiscriminatorMapping;

final class AttributeClassDiscriminatorMappingFactory
{
    /** @var iterable<\Ibexa\Bundle\ProductCatalog\Serializer\AttributeValueMappingFactoryInterface> */
    private iterable $mappingFactories;

    /**
     * @param iterable<\Ibexa\Bundle\ProductCatalog\Serializer\AttributeValueMappingFactoryInterface> $mappingFactories
     */
    public function __construct(iterable $mappingFactories = [])
    {
        $this->mappingFactories = $mappingFactories;
    }

    public function getMapping(): ClassDiscriminatorMapping
    {
        $mappings = [];
        foreach ($this->mappingFactories as $mappingFactory) {
            $mappings = array_merge($mappings, $mappingFactory->getMapping());
        }

        return new ClassDiscriminatorMapping('type', $mappings);
    }
}
