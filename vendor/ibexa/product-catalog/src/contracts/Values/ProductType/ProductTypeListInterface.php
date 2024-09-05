<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\ProductType;

use IteratorAggregate;

/**
 * @extends  \IteratorAggregate<int, \Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface>
 */
interface ProductTypeListInterface extends IteratorAggregate
{
    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface[]
     */
    public function getProductTypes(): array;

    public function getTotalCount(): int;
}
