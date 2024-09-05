<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Region;

use Ibexa\Contracts\ProductCatalog\Values\RegionInterface;
use Ibexa\Core\Base\Exceptions\NotFoundException;

final class RegionPool implements RegionPoolInterface
{
    /** @var array<string,\Ibexa\Contracts\ProductCatalog\Values\RegionInterface> */
    private array $regions;

    /**
     * @param array<string,\Ibexa\Contracts\ProductCatalog\Values\RegionInterface> $regions
     */
    public function __construct(array $regions)
    {
        $this->regions = $regions;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function getRegion(string $identifier): RegionInterface
    {
        if (isset($this->regions[$identifier])) {
            return $this->regions[$identifier];
        }

        throw new NotFoundException(RegionInterface::class, $identifier);
    }

    /**
     * @return iterable<array-key,\Ibexa\Contracts\ProductCatalog\Values\RegionInterface>
     */
    public function getRegions(): iterable
    {
        return $this->regions;
    }
}
