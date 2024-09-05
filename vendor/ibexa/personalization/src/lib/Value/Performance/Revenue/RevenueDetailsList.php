<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Performance\Revenue;

use ArrayAccess;
use ArrayIterator;
use BadMethodCallException;
use Countable;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Exceptions\OutOfBoundsException;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
 * @implements IteratorAggregate<int, \Ibexa\Personalization\Value\Performance\Revenue\RevenueDetails>
 * @implements ArrayAccess<int, \Ibexa\Personalization\Value\Performance\Revenue\RevenueDetails>
 */
final class RevenueDetailsList implements IteratorAggregate, ArrayAccess, JsonSerializable, Countable
{
    /** @var array<\Ibexa\Personalization\Value\Performance\Revenue\RevenueDetails> */
    private array $revenueDetailsList;

    /**
     * @param array<\Ibexa\Personalization\Value\Performance\Revenue\RevenueDetails> $revenueDetailsList
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function __construct(array $revenueDetailsList)
    {
        foreach ($revenueDetailsList as $revenueDetails) {
            if (!$revenueDetails instanceof RevenueDetails) {
                /** @var mixed $revenueDetails */
                throw new InvalidArgumentException(
                    '$revenueDetails',
                    sprintf(
                        'Must be of type: %s, %s given',
                        RevenueDetails::class,
                        is_object($revenueDetails) ? get_class($revenueDetails) : gettype($revenueDetails)
                    )
                );
            }
        }

        $this->revenueDetailsList = $revenueDetailsList;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->revenueDetailsList);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->revenueDetailsList[$offset]);
    }

    public function offsetGet($offset): RevenueDetails
    {
        if (false === $this->offsetExists($offset)) {
            throw new OutOfBoundsException(
                sprintf('The collection does not contain an element with index: %d', $offset)
            );
        }

        return $this->revenueDetailsList[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        throw new BadMethodCallException(
            'Unsupported method'
        );
    }

    public function offsetUnset($offset): void
    {
        unset($this->revenueDetailsList[$offset]);
    }

    /**
     * @return array<\Ibexa\Personalization\Value\Performance\Revenue\RevenueDetails>
     */
    public function slice(int $offset, int $length): array
    {
        return array_slice($this->revenueDetailsList, $offset, $length);
    }

    public function count(): int
    {
        return count($this->revenueDetailsList);
    }

    /**
     * @return array<\Ibexa\Personalization\Value\Performance\Revenue\RevenueDetails>
     */
    public function jsonSerialize(): array
    {
        return $this->revenueDetailsList;
    }
}

class_alias(RevenueDetailsList::class, 'Ibexa\Platform\Personalization\Value\Performance\Revenue\RevenueDetailsList');
