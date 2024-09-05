<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Calendar\EventAction;

use ArrayIterator;
use InvalidArgumentException;
use Iterator;
use IteratorAggregate;

/**
 * @final
 */
class EventActionCollection implements IteratorAggregate
{
    /** @var \Ibexa\Contracts\Calendar\EventAction\EventActionInterface[] */
    private array $actions;

    /**
     * @param iterable<\Ibexa\Contracts\Calendar\EventAction\EventActionInterface> $actions
     */
    public function __construct(iterable $actions = [])
    {
        $this->actions = [];
        foreach ($actions as $action) {
            $this->actions[$action->getActionIdentifier()] = $action;
        }
    }

    /**
     * Returns true if action with given identifier is supported.
     */
    public function supports(string $identifier): bool
    {
        return isset($this->actions[$identifier]);
    }

    /**
     * Returns action definition with given identifier.
     */
    public function get(string $identifier): EventActionInterface
    {
        if (!$this->supports($identifier)) {
            throw new InvalidArgumentException("Action $identifier is not supported.");
        }

        return $this->actions[$identifier];
    }

    public function isEmpty(): bool
    {
        return empty($this->actions);
    }

    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->actions);
    }

    public static function createEmpty(): self
    {
        return new self();
    }
}
