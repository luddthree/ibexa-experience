<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Value;

use ArrayIterator;
use Traversable;

class WorkflowMarkingCollection implements \ArrayAccess, \IteratorAggregate
{
    /** @var \Ibexa\Workflow\Value\MarkingMetadata[] */
    protected $markings;

    /**
     * @param \Ibexa\Workflow\Value\MarkingMetadata[] $markings
     */
    public function __construct(array $markings)
    {
        $this->markings = $markings;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->markings[$offset]);
    }

    public function offsetGet($offset): MarkingMetadata
    {
        return $this->markings[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        $this->markings[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->markings[$offset]);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->markings);
    }
}

class_alias(WorkflowMarkingCollection::class, 'EzSystems\EzPlatformWorkflow\Value\WorkflowMarkingCollection');
