<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

final class AttributeGroupBulkDeleteData
{
    /** @var \Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface[] */
    private array $attributeGroups;

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface[] $attributeGroups
     */
    public function __construct(array $attributeGroups = [])
    {
        $this->attributeGroups = $attributeGroups;
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface[]
     */
    public function getAttributeGroups(): array
    {
        return $this->attributeGroups;
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface[] $attributeGroups
     */
    public function setAttributeGroups(array $attributeGroups): void
    {
        $this->attributeGroups = $attributeGroups;
    }
}
