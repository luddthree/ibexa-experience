<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Values;

use ArrayIterator;
use Ibexa\Contracts\ProductCatalog\Values\Region\RegionListInterface;
use Traversable;

final class RegionList implements RegionListInterface
{
    /** @var \Ibexa\Contracts\ProductCatalog\Values\RegionInterface[] */
    private array $regions;

    private int $totalCount;

    /**
     * @param array<\Ibexa\Contracts\ProductCatalog\Values\RegionInterface> $regions
     */
    public function __construct(array $regions, int $totalCount)
    {
        $this->regions = $regions;
        $this->totalCount = $totalCount;
    }

    public function count(): int
    {
        return count($this->regions);
    }

    public function getRegions(): array
    {
        return $this->regions;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    /**
     * @return \Traversable<\Ibexa\Contracts\ProductCatalog\Values\RegionInterface>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->regions);
    }
}
