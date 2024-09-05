<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Rest\Value;

final class VatCategoryList extends Value
{
    /**
     * @var \Ibexa\Bundle\ProductCatalog\REST\Value\VatCategory[]
     */
    public array $vatCategories = [];

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\VatCategory[] $vatCategories
     */
    public function __construct(array $vatCategories)
    {
        $this->vatCategories = $vatCategories;
    }
}
