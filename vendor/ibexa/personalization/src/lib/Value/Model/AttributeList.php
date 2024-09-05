<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Model;

use ArrayAccess;
use ArrayIterator;
use BadMethodCallException;
use Countable;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
 * @implements IteratorAggregate<array-key, \Ibexa\Personalization\Value\Model\Attribute>
 * @implements ArrayAccess<array-key, \Ibexa\Personalization\Value\Model\Attribute>
 */
final class AttributeList implements IteratorAggregate, ArrayAccess, JsonSerializable, Countable
{
    /** @var \Ibexa\Personalization\Value\Model\Attribute[] */
    private $attributeList;

    public function __construct(array $attributeList)
    {
        foreach ($attributeList as $attribute) {
            if (false === $attribute instanceof Attribute) {
                throw new InvalidArgumentException(
                    '$attribute',
                    sprintf(
                        'Must be of type: %s, %s given',
                        Attribute::class,
                        is_object($attribute) ? get_class($attribute) : gettype($attribute)
                    )
                );
            }
        }
        $this->attributeList = $attributeList;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->attributeList);
    }

    public function offsetUnset($offset): void
    {
        unset($this->attributeList[$offset]);
    }

    public function offsetSet($offset, $value): void
    {
        throw new BadMethodCallException('Unsupported method');
    }

    public function offsetGet($offset): Attribute
    {
        return $this->attributeList[$offset];
    }

    public function offsetExists($offset): bool
    {
        return isset($this->attributeList[$offset]);
    }

    public function count(): int
    {
        return count($this->attributeList);
    }

    public function slice(int $offset, int $length): array
    {
        return array_slice($this->attributeList, $offset, $length);
    }

    public function jsonSerialize(): array
    {
        return $this->attributeList;
    }
}

class_alias(AttributeList::class, 'Ibexa\Platform\Personalization\Value\Model\AttributeList');
