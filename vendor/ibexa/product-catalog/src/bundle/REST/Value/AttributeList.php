<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Rest\Value;

final class AttributeList extends Value
{
    /**
     * @var \Ibexa\Bundle\ProductCatalog\REST\Value\Attribute[]
     */
    public array $attributes = [];

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\Attribute[] $attributes
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }
}
