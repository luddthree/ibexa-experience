<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Model;

use ArrayAccess;
use ArrayIterator;
use Ibexa\Personalization\Client\Consumer\Model\GroupingOperation;
use IteratorAggregate;
use JsonSerializable;
use Traversable;
use Webmozart\Assert\Assert;

/**
 * @implements IteratorAggregate<array-key, \Ibexa\Personalization\Value\Model\SegmentItemGroupElement>
 * @implements ArrayAccess<array-key, \Ibexa\Personalization\Value\Model\SegmentItemGroupElement>
 */
final class SegmentItemGroup implements JsonSerializable, ArrayAccess, IteratorAggregate
{
    private int $id;

    /** @var array<\Ibexa\Personalization\Value\Model\SegmentItemGroupElement> */
    private array $groupElements;

    /** @phpstan-var \Ibexa\Personalization\Client\Consumer\Model\GroupingOperation::* */
    private string $groupingOperation;

    /**
     * @phpstan-param \Ibexa\Personalization\Client\Consumer\Model\GroupingOperation::* $groupingOperation
     *
     * @param array<\Ibexa\Personalization\Value\Model\SegmentItemGroupElement> $groupElements
     */
    public function __construct(
        int $id,
        string $groupingOperation,
        array $groupElements = []
    ) {
        Assert::allIsInstanceOf($groupElements, SegmentItemGroupElement::class);

        $this->id = $id;
        $this->groupingOperation = $groupingOperation;
        $this->groupElements = $groupElements;
    }

    /**
     * @return array<\Ibexa\Personalization\Value\Model\SegmentItemGroupElement>
     */
    public function getGroupElements(): array
    {
        return $this->groupElements;
    }

    /**
     * @param array<\Ibexa\Personalization\Value\Model\SegmentItemGroupElement> $groupElements
     */
    public function setGroupElements(array $groupElements): void
    {
        $this->groupElements = $groupElements;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @phpstan-return \Ibexa\Personalization\Client\Consumer\Model\GroupingOperation::*
     */
    public function getGroupingOperation(): string
    {
        return $this->groupingOperation;
    }

    /**
     * @phpstan-param \Ibexa\Personalization\Client\Consumer\Model\GroupingOperation::* $groupingOperation
     */
    public function setGroupingOperation(string $groupingOperation): void
    {
        $this->groupingOperation = $groupingOperation;
    }

    /**
     * @phpstan-return array{
     *      'id': int,
     *      'groupingOperation': \Ibexa\Personalization\Client\Consumer\Model\GroupingOperation::*,
     *      'groupElements': array<\Ibexa\Personalization\Value\Model\SegmentItemGroupElement>
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'groupingOperation' => $this->groupingOperation,
            'groupElements' => $this->groupElements,
        ];
    }

    public function offsetExists($offset): bool
    {
        return isset($this->groupElements[$offset]);
    }

    public function offsetGet($offset): SegmentItemGroupElement
    {
        return $this->groupElements[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        $this->groupElements[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->groupElements[$offset]);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->groupElements);
    }
}
