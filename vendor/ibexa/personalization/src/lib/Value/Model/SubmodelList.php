<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Model;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
 * @implements IteratorAggregate<array-key, \Ibexa\Personalization\Value\Model\Submodel>
 * @implements ArrayAccess<array-key, \Ibexa\Personalization\Value\Model\Submodel>
 */
final class SubmodelList implements IteratorAggregate, ArrayAccess, JsonSerializable, Countable
{
    /** @var \Ibexa\Personalization\Value\Model\Submodel[] */
    private $submodelList;

    public function __construct(array $submodelList = [])
    {
        foreach ($submodelList as $submodel) {
            if (false === $submodel instanceof Submodel) {
                throw new InvalidArgumentException(
                    '$submodel',
                    sprintf(
                        'Must be of type: %s, %s given',
                        Submodel::class,
                        is_object($submodel) ? get_class($submodel) : gettype($submodel)
                    )
                );
            }
        }
        $this->submodelList = $submodelList;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->submodelList);
    }

    public function offsetUnset($offset): void
    {
        unset($this->submodelList[$offset]);
    }

    public function offsetSet($offset, $value): void
    {
        $this->submodelList[$offset] = $value;
    }

    public function offsetGet($offset): Submodel
    {
        return $this->submodelList[$offset];
    }

    public function offsetExists($offset): bool
    {
        return isset($this->submodelList[$offset]);
    }

    public function count(): int
    {
        return count($this->submodelList);
    }

    public function slice(int $offset, int $length): array
    {
        return array_slice($this->submodelList, $offset, $length);
    }

    public function jsonSerialize(): array
    {
        return $this->submodelList;
    }
}

class_alias(SubmodelList::class, 'Ibexa\Platform\Personalization\Value\Model\SubmodelList');
