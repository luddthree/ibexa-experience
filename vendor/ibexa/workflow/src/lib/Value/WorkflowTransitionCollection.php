<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Value;

use ArrayIterator;
use Traversable;

class WorkflowTransitionCollection implements \ArrayAccess, \IteratorAggregate
{
    /** @var \Ibexa\Workflow\Value\WorkflowTransition[] */
    protected $transitions;

    /**
     * @param \Ibexa\Workflow\Value\WorkflowTransition[] $transitions
     */
    public function __construct(array $transitions)
    {
        $this->transitions = $transitions;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->transitions[$offset]);
    }

    public function offsetGet($offset): WorkflowTransition
    {
        return $this->transitions[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        $this->transitions[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->transitions[$offset]);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->transitions);
    }
}

class_alias(WorkflowTransitionCollection::class, 'EzSystems\EzPlatformWorkflow\Value\WorkflowTransitionCollection');
