<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\Rest\Value;

final class AttributeGroup extends Value
{
    public AttributeGroupInterface $attributeGroup;

    public function __construct(AttributeGroupInterface $attributeGroup)
    {
        $this->attributeGroup = $attributeGroup;
    }
}
