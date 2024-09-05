<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Rest\Value;

final class AttributeGroupList extends Value
{
    /**
     * @var \Ibexa\Bundle\ProductCatalog\REST\Value\AttributeGroup[]
     */
    public array $attributeGroups = [];

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\AttributeGroup[] $attributeGroups
     */
    public function __construct(array $attributeGroups)
    {
        $this->attributeGroups = $attributeGroups;
    }
}
