<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\ProductCatalog\Values\VatCategoryInterface;
use Ibexa\Rest\Value;

final class VatCategory extends Value
{
    public VatCategoryInterface $vatCategory;

    public function __construct(VatCategoryInterface $vatCategory)
    {
        $this->vatCategory = $vatCategory;
    }
}
