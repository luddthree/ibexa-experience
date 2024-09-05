<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Import;

use ArrayAccess;
use ArrayIterator;
use BadMethodCallException;
use Countable;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

final class ImportedItemList implements IteratorAggregate, ArrayAccess, JsonSerializable, Countable
{
    /** @var \Ibexa\Personalization\Value\Import\ImportedItem[] */
    private $importedItemList;

    public function __construct(array $importedItemList)
    {
        foreach ($importedItemList as $importedItem) {
            if (false === $importedItem instanceof ImportedItem) {
                throw new InvalidArgumentException(
                    '$importedItem',
                    sprintf(
                        'Must be of type: %s, %s given',
                        ImportedItem::class,
                        is_object($importedItem) ? get_class($importedItem) : gettype($importedItem)
                    )
                );
            }
        }
        $this->importedItemList = $importedItemList;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->importedItemList);
    }

    public function offsetUnset($offset): void
    {
        unset($this->importedItemList[$offset]);
    }

    public function offsetSet($offset, $value): void
    {
        throw new BadMethodCallException('Unsupported method');
    }

    public function offsetGet($offset): ImportedItem
    {
        return $this->importedItemList[$offset];
    }

    public function offsetExists($offset): bool
    {
        return isset($this->importedItemList[$offset]);
    }

    public function count(): int
    {
        return count($this->importedItemList);
    }

    public function slice(int $offset, int $length): array
    {
        return array_slice($this->importedItemList, $offset, $length);
    }

    public function jsonSerialize(): array
    {
        return $this->importedItemList;
    }
}

class_alias(ImportedItemList::class, 'Ibexa\Platform\Personalization\Value\Import\ImportedItemList');
