<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Connector\Dam\Search;

use Ibexa\Contracts\Connector\Dam\AssetCollection;
use Iterator;
use IteratorAggregate;
use IteratorIterator;

class AssetSearchResult implements IteratorAggregate
{
    /** @var int */
    protected $totalCount;

    /** @var \Ibexa\Contracts\Connector\Dam\AssetCollection */
    protected $results;

    public function __construct(int $totalCount, AssetCollection $results)
    {
        $this->totalCount = $totalCount;
        $this->results = $results;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    public function getResults(): AssetCollection
    {
        return $this->results;
    }

    public function getIterator(): Iterator
    {
        return new IteratorIterator($this->results);
    }
}

class_alias(AssetSearchResult::class, 'Ibexa\Platform\Contracts\Connector\Dam\Search\AssetSearchResult');
