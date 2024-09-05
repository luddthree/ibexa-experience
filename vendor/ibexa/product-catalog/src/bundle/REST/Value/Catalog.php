<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use Ibexa\Rest\Value;

final class Catalog extends Value
{
    public CatalogInterface $catalog;

    public function __construct(CatalogInterface $catalog)
    {
        $this->catalog = $catalog;
    }
}
