<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Content;

use ArrayAccess;
use ArrayIterator;
use BadMethodCallException;
use Ibexa\Contracts\Core\Repository\Exceptions\OutOfBoundsException;
use IteratorAggregate;
use JsonSerializable;
use Traversable;
use Webmozart\Assert\Assert;

/**
 * @implements ArrayAccess<int, \Ibexa\Personalization\Value\Content\AbstractItemType>
 * @implements IteratorAggregate<int, \Ibexa\Personalization\Value\Content\AbstractItemType>
 */
final class ItemTypeList implements IteratorAggregate, ArrayAccess, JsonSerializable
{
    /** @var array<\Ibexa\Personalization\Value\Content\AbstractItemType> */
    private array $itemTypeList;

    /**
     * @param array<\Ibexa\Personalization\Value\Content\AbstractItemType> $itemTypeList
     */
    public function __construct(array $itemTypeList)
    {
        Assert::allIsInstanceOf($itemTypeList, AbstractItemType::class);

        $this->itemTypeList = $itemTypeList;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->itemTypeList);
    }

    public function offsetUnset($offset): void
    {
        unset($this->itemTypeList[$offset]);
    }

    public function offsetSet($offset, $value): void
    {
        throw new BadMethodCallException('Unsupported method');
    }

    public function offsetGet($offset): AbstractItemType
    {
        return $this->itemTypeList[$offset];
    }

    public function offsetExists($offset): bool
    {
        return isset($this->itemTypeList[$offset]);
    }

    public function findItemType(int $itemTypeId): ?ItemType
    {
        foreach ($this->itemTypeList as $item) {
            if ($item instanceof ItemType && $itemTypeId === $item->getId()) {
                return $item;
            }
        }

        return null;
    }

    public function getFirst(): AbstractItemType
    {
        if (empty($this->itemTypeList)) {
            throw new OutOfBoundsException('Collection is empty');
        }

        return reset($this->itemTypeList);
    }

    /**
     * @return array<\Ibexa\Personalization\Value\Content\ItemType>
     */
    public function getItemTypes(): array
    {
        return array_filter($this->itemTypeList, static function (AbstractItemType $itemType) {
            return $itemType instanceof ItemType;
        });
    }

    /**
     * @return array<string>
     */
    public function getItemTypesDescriptions(): array
    {
        $itemTypesDescriptions = [];
        foreach ($this->itemTypeList as $itemType) {
            $itemTypesDescriptions[] = $itemType->getDescription();
        }

        return $itemTypesDescriptions;
    }

    public function jsonSerialize(): array
    {
        $itemTypeIdList = [];
        foreach ($this->itemTypeList as $itemType) {
            if ($itemType instanceof ItemType) {
                $itemTypeIdList[] = $itemType->getId();
            }
        }

        return $itemTypeIdList;
    }
}

class_alias(ItemTypeList::class, 'Ibexa\Platform\Personalization\Value\Content\ItemTypeList');
