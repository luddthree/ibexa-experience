<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Model;

use ArrayAccess;
use ArrayIterator;
use Ibexa\Segmentation\Value\Segment as ApiSegmentValue;
use IteratorAggregate;
use JsonSerializable;
use Traversable;
use Webmozart\Assert\Assert;

/**
 * @implements IteratorAggregate<array-key, \Ibexa\Personalization\Value\Model\Segment>
 * @implements ArrayAccess<array-key, \Ibexa\Personalization\Value\Model\Segment>
 */
final class SegmentList implements ArrayAccess, IteratorAggregate, JsonSerializable
{
    /** @var \Ibexa\Personalization\Value\Model\Segment[] */
    private array $segments;

    /**
     * @param \Ibexa\Personalization\Value\Model\Segment[] $segments
     */
    public function __construct(
        array $segments
    ) {
        Assert::allIsInstanceOf($segments, Segment::class);

        $this->segments = $segments;
    }

    /**
     * @return \Ibexa\Personalization\Value\Model\Segment[]
     */
    public function getSegments(): array
    {
        return $this->segments;
    }

    public function count(): int
    {
        return count($this->segments);
    }

    /**
     * @param array<\Ibexa\Segmentation\Value\Segment> $segments
     */
    public static function fromArray(array $segments): self
    {
        return new self(
            array_map(
                static fn (ApiSegmentValue $segment): Segment => new Segment(
                    $segment->name,
                    $segment->id,
                    new SegmentGroup(
                        $segment->group->id,
                        $segment->group->name,
                    )
                ),
                $segments
            ),
        );
    }

    public function offsetExists($offset): bool
    {
        return isset($this->segments[$offset]);
    }

    public function offsetGet($offset): Segment
    {
        return $this->segments[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        $this->segments[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->segments[$offset]);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->segments);
    }

    /**
     * @return \Ibexa\Personalization\Value\Model\Segment[]
     */
    public function jsonSerialize(): array
    {
        return [...$this];
    }
}
