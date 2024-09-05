<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Performance\Summary;

use ArrayAccess;
use ArrayIterator;
use BadMethodCallException;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
 * @implements IteratorAggregate<int, \Ibexa\Personalization\Value\Performance\Summary\ConversionRate>
 * @implements ArrayAccess<int, \Ibexa\Personalization\Value\Performance\Summary\ConversionRate>
 */
final class ConversionRateList implements IteratorAggregate, ArrayAccess, JsonSerializable
{
    /** @var array<\Ibexa\Personalization\Value\Performance\Summary\ConversionRate> */
    private array $conversionRateList;

    /**
     * @param array<\Ibexa\Personalization\Value\Performance\Summary\ConversionRate> $conversionRateList
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function __construct(array $conversionRateList)
    {
        foreach ($conversionRateList as $conversionRate) {
            if (!$conversionRate instanceof ConversionRate) {
                /** @var mixed $conversionRate */
                throw new InvalidArgumentException(
                    '$conversionRate',
                    sprintf(
                        'Must be of type: %s, %s given',
                        ConversionRate::class,
                        is_object($conversionRate) ? get_class($conversionRate) : gettype($conversionRate)
                    )
                );
            }
        }
        $this->conversionRateList = $conversionRateList;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->conversionRateList);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->conversionRateList[$offset]);
    }

    public function offsetGet($offset): ConversionRate
    {
        return $this->conversionRateList[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        throw new BadMethodCallException(
            'Unsupported method'
        );
    }

    public function offsetUnset($offset): void
    {
        unset($this->conversionRateList[$offset]);
    }

    /**
     * @return array<\Ibexa\Personalization\Value\Performance\Summary\ConversionRate>
     */
    public function jsonSerialize(): array
    {
        return $this->conversionRateList;
    }

    public function findById(string $id): ?ConversionRate
    {
        foreach ($this->conversionRateList as $conversionRate) {
            if ($id === $conversionRate->getId()) {
                return $conversionRate;
            }
        }

        return null;
    }
}

class_alias(ConversionRateList::class, 'Ibexa\Platform\Personalization\Value\Performance\Summary\ConversionRateList');
