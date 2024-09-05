<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Performance\Popularity;

use ArrayAccess;
use ArrayIterator;
use BadMethodCallException;
use Ibexa\Contracts\Core\Repository\Exceptions\OutOfBoundsException;
use IteratorAggregate;
use JsonSerializable;
use Traversable;
use Webmozart\Assert\Assert;

/**
 * @implements IteratorAggregate<int, \Ibexa\Personalization\Value\Performance\Popularity\Popularity>
 * @implements ArrayAccess<int, \Ibexa\Personalization\Value\Performance\Popularity\Popularity>
 */
final class PopularityList implements IteratorAggregate, ArrayAccess, JsonSerializable
{
    /** @var array<\Ibexa\Personalization\Value\Performance\Popularity\Popularity> */
    private array $popularityList;

    /**
     * @param array<\Ibexa\Personalization\Value\Performance\Popularity\Popularity> $popularityList
     */
    public function __construct(array $popularityList)
    {
        Assert::allIsInstanceOf($popularityList, Popularity::class);

        $this->popularityList = $popularityList;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->popularityList);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->popularityList[$offset]);
    }

    public function offsetGet($offset): Popularity
    {
        if (false === $this->offsetExists($offset)) {
            throw new OutOfBoundsException(
                sprintf('The collection does not contain an element with index: %d', $offset)
            );
        }

        return $this->popularityList[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        throw new BadMethodCallException(
            'Unsupported method'
        );
    }

    public function offsetUnset($offset): void
    {
        unset($this->popularityList[$offset]);
    }

    /**
     * @return array<\Ibexa\Personalization\Value\Performance\Popularity\Popularity>
     */
    public function jsonSerialize(): array
    {
        return $this->popularityList;
    }
}
