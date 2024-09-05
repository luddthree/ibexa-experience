<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Performance\Details;

use ArrayAccess;
use ArrayIterator;
use BadMethodCallException;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Exceptions\OutOfBoundsException;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
 * @implements IteratorAggregate<int, \Ibexa\Personalization\Value\Performance\Details\Revenue>
 * @implements ArrayAccess<int, \Ibexa\Personalization\Value\Performance\Details\Revenue>
 */
final class RevenueList implements IteratorAggregate, ArrayAccess, JsonSerializable
{
    /** @var array<\Ibexa\Personalization\Value\Performance\Details\Revenue> */
    private array $revenueList;

    /**
     * @param array<\Ibexa\Personalization\Value\Performance\Details\Revenue> $revenueList
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function __construct(array $revenueList)
    {
        foreach ($revenueList as $revenue) {
            if (!$revenue instanceof Revenue) {
                /** @var mixed $revenue */
                throw new InvalidArgumentException(
                    '$revenue',
                    sprintf(
                        'Must be of type: %s, %s given',
                        Revenue::class,
                        is_object($revenue) ? get_class($revenue) : gettype($revenue)
                    )
                );
            }
        }

        $this->revenueList = $revenueList;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->revenueList);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->revenueList[$offset]);
    }

    public function offsetGet($offset): Revenue
    {
        if (false === $this->offsetExists($offset)) {
            throw new OutOfBoundsException(
                sprintf('The collection does not contain an element with index: %d', $offset)
            );
        }

        return $this->revenueList[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        throw new BadMethodCallException(
            'Unsupported method'
        );
    }

    public function offsetUnset($offset): void
    {
        unset($this->revenueList[$offset]);
    }

    /**
     * @return array<\Ibexa\Personalization\Value\Performance\Details\Revenue>
     */
    public function jsonSerialize(): array
    {
        return $this->revenueList;
    }
}

class_alias(RevenueList::class, 'Ibexa\Platform\Personalization\Value\Performance\Details\RevenueList');
