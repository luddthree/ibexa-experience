<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\VatCategory;

use Countable;
use IteratorAggregate;

/**
 * @extends \IteratorAggregate<\Ibexa\Contracts\ProductCatalog\Values\VatCategoryInterface>
 */
interface VatCategoryListInterface extends IteratorAggregate, Countable
{
    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\VatCategoryInterface[]
     */
    public function getVatCategories(): array;

    /**
     * Return total count of found categories.
     */
    public function getTotalCount(): int;
}
