<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Permission\Policy\Product;

use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;

abstract class AbstractProductPolicy implements PolicyInterface
{
    public function getModule(): string
    {
        return 'product';
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\ValueObject[]
     */
    public function getTargets(): array
    {
        return [];
    }
}
