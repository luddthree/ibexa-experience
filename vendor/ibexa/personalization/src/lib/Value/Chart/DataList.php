<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Chart;

use ArrayAccess;
use ArrayIterator;
use BadMethodCallException;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
 * @implements IteratorAggregate<int, \Ibexa\Personalization\Value\Chart\Data>
 * @implements ArrayAccess<int,\Ibexa\Personalization\Value\Chart\Data>
 */
final class DataList implements IteratorAggregate, ArrayAccess, JsonSerializable
{
    /** @var array<\Ibexa\Personalization\Value\Chart\Data> */
    private array $dataList;

    /**
     * @phpstan-param array<\Ibexa\Personalization\Value\Chart\Data> $dataList
     *
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    public function __construct(array $dataList = [])
    {
        foreach ($dataList as $data) {
            if (!$data instanceof Data) {
                /** @var mixed $data */
                throw new InvalidArgumentException(
                    '$data',
                    sprintf(
                        'Must be of type: %s, %s given',
                        Data::class,
                        is_object($data) ? get_class($data) : gettype($data)
                    )
                );
            }
        }
        $this->dataList = $dataList;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->dataList);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->dataList[$offset]);
    }

    public function offsetGet($offset): Data
    {
        return $this->dataList[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        throw new BadMethodCallException(
            'Unsupported method'
        );
    }

    public function offsetUnset($offset): void
    {
        unset($this->dataList[$offset]);
    }

    /**
     * @return array<\Ibexa\Personalization\Value\Chart\Data>
     */
    public function jsonSerialize(): array
    {
        return $this->dataList;
    }

    public function push(Data $data): void
    {
        $this->dataList[] = $data;
    }
}

class_alias(DataList::class, 'Ibexa\Platform\Personalization\Value\Chart\DataList');
