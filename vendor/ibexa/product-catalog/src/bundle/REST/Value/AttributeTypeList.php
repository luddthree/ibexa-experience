<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Rest\Value;

final class AttributeTypeList extends Value
{
    /**
     * @var \Ibexa\Bundle\ProductCatalog\REST\Value\AttributeType[]
     */
    public array $attributeTypes = [];

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\AttributeType[] $attributeTypes
     */
    public function __construct(array $attributeTypes)
    {
        $this->attributeTypes = $attributeTypes;
    }
}
