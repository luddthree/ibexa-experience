<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\ProductCatalog\Values\Region\RegionListInterface;
use Ibexa\Rest\Value;

final class RegionView extends Value
{
    private string $identifier;

    private RegionListInterface $regionList;

    public function __construct(string $identifier, RegionListInterface $regionList)
    {
        $this->identifier = $identifier;
        $this->regionList = $regionList;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getRegionList(): RegionListInterface
    {
        return $this->regionList;
    }
}
