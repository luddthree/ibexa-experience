<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

final class AttributeDefinitionBulkDeleteData
{
    /** @var \Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface[] */
    private array $attributesDefinitions;

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface[] $attributesDefinitions
     */
    public function __construct(array $attributesDefinitions = [])
    {
        $this->attributesDefinitions = $attributesDefinitions;
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface[]
     */
    public function getAttributesDefinitions(): array
    {
        return $this->attributesDefinitions;
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface[] $attributesDefinitions
     */
    public function setAttributesDefinitions(array $attributesDefinitions): void
    {
        $this->attributesDefinitions = $attributesDefinitions;
    }
}
