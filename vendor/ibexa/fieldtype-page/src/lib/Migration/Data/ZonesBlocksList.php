<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Migration\Data;

use IteratorAggregate;
use Traversable;

/**
 * Zone name to the list of block map.
 *
 * @internal
 *
 * @implements \IteratorAggregate<string, array<\Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue>>
 */
final class ZonesBlocksList implements IteratorAggregate
{
    /** @var array<string, array<\Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue>> */
    private array $zonesBlocksList;

    public function __construct()
    {
        $this->zonesBlocksList = [];
    }

    /**
     * @param array<\Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue> $blockValueList
     */
    public function addZoneBlocks(string $name, array $blockValueList): void
    {
        $this->zonesBlocksList[$name] = $blockValueList;
    }

    /**
     * @return \Traversable<string, array<\Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue>>
     */
    public function getIterator(): Traversable
    {
        yield from $this->zonesBlocksList;
    }
}
