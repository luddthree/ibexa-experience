<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeGroup;

use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;

abstract class AbstractAttributeGroupPolicy implements PolicyInterface
{
    public function getModule(): string
    {
        return 'product_type';
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\ValueObject[]
     */
    public function getTargets(): array
    {
        return [];
    }
}
