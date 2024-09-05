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
 * @implements IteratorAggregate<array-key, \Ibexa\Personalization\Value\Model\Model>
 * @implements ArrayAccess<array-key, \Ibexa\Personalization\Value\Model\Model>
 */
final class ModelList implements IteratorAggregate, ArrayAccess, JsonSerializable, Countable
{
    /** @var \Ibexa\Personalization\Value\Model\Model[] */
    private $modelList;

    public function __construct(array $modelList)
    {
        foreach ($modelList as $model) {
            if (false === $model instanceof Model) {
                throw new InvalidArgumentException(
                    '$model',
                    sprintf(
                        'Must be of type: %s, %s given',
                        Model::class,
                        is_object($model) ? get_class($model) : gettype($model)
                    )
                );
            }
        }
        $this->modelList = $modelList;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->modelList);
    }

    public function offsetUnset($offset): void
    {
        unset($this->modelList[$offset]);
    }

    public function offsetSet($offset, $value): void
    {
        throw new BadMethodCallException('Unsupported method');
    }

    public function offsetGet($offset): Model
    {
        return $this->modelList[$offset];
    }

    public function offsetExists($offset): bool
    {
        return isset($this->modelList[$offset]);
    }

    public function count(): int
    {
        return count($this->modelList);
    }

    public function slice(int $offset, int $length): array
    {
        return array_slice($this->modelList, $offset, $length);
    }

    public function jsonSerialize(): array
    {
        return $this->modelList;
    }
}

class_alias(ModelList::class, 'Ibexa\Platform\Personalization\Value\Model\ModelList');
