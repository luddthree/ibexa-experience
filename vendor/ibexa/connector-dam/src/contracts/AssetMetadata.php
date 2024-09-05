<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Connector\Dam;

use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use RuntimeException;

final class AssetMetadata implements ArrayAccess, IteratorAggregate
{
    /** @var array */
    private $metadata;

    public function __construct(array $metadata)
    {
        $this->metadata = $metadata;
    }

    public function offsetExists($offset)
    {
        return isset($this->metadata[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->metadata[$offset];
    }

    public function offsetSet($offset, $value)
    {
        throw new RuntimeException('Metadata is readonly');
    }

    public function offsetUnset($offset)
    {
        throw new RuntimeException('Metadata is readonly');
    }

    public function getIterator()
    {
        return new ArrayIterator($this->metadata);
    }
}

class_alias(AssetMetadata::class, 'Ibexa\Platform\Contracts\Connector\Dam\AssetMetadata');
