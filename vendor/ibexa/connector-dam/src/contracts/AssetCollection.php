<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Connector\Dam;

use ArrayIterator;
use IteratorAggregate;

final class AssetCollection implements IteratorAggregate
{
    /** @var \Ibexa\Contracts\Connector\Dam\Asset[] */
    private $assets;

    /**
     * @param \Ibexa\Contracts\Connector\Dam\Asset[] $assets
     */
    public function __construct(array $assets)
    {
        $this->assets = $assets;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->assets);
    }

    /**
     * @return \Ibexa\Contracts\Connector\Dam\Asset[]
     */
    public function toArray(): array
    {
        return $this->assets;
    }
}

class_alias(AssetCollection::class, 'Ibexa\Platform\Contracts\Connector\Dam\AssetCollection');
