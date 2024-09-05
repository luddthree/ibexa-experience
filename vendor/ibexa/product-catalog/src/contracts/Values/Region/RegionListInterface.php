<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Region;

use Countable;
use IteratorAggregate;

/**
 * @extends \IteratorAggregate<\Ibexa\Contracts\ProductCatalog\Values\RegionInterface>
 */
interface RegionListInterface extends IteratorAggregate, Countable
{
    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\RegionInterface[]
     */
    public function getRegions(): array;

    /**
     * Return total count of found categories.
     */
    public function getTotalCount(): int;
}
