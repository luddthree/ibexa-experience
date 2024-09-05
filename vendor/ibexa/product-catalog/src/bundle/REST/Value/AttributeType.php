<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;
use Ibexa\Rest\Value;

final class AttributeType extends Value
{
    public AttributeTypeInterface $attributeType;

    public function __construct(AttributeTypeInterface $attributeType)
    {
        $this->attributeType = $attributeType;
    }
}
