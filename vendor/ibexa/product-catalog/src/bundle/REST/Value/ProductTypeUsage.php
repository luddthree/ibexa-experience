<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Rest\Value;

final class ProductTypeUsage extends Value
{
    public bool $isUsed;

    public function __construct(bool $isUsed)
    {
        $this->isUsed = $isUsed;
    }
}
