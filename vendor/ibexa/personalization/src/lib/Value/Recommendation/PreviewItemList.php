<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Recommendation;

use ArrayAccess;
use ArrayIterator;
use BadMethodCallException;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

final class PreviewItemList implements IteratorAggregate, ArrayAccess, JsonSerializable
{
    /** @var \Ibexa\Personalization\Value\Recommendation\PreviewItem[] */
    private $previewItemList;

    public function __construct(array $previewItemList)
    {
        foreach ($previewItemList as $previewItem) {
            if (!$previewItem instanceof PreviewItem) {
                throw new InvalidArgumentException(
                    '$previewItem',
                    sprintf(
                        'Must be of type: %s, %s given',
                        PreviewItem::class,
                        is_object($previewItem) ? get_class($previewItem) : gettype($previewItem)
                    )
                );
            }
        }

        $this->previewItemList = $previewItemList;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->previewItemList);
    }

    public function offsetUnset($offset): void
    {
        unset($this->previewItemList[$offset]);
    }

    public function offsetSet($offset, $value): void
    {
        throw new BadMethodCallException('Unsupported method');
    }

    public function offsetGet($offset): PreviewItem
    {
        return $this->previewItemList[$offset];
    }

    public function offsetExists($offset): bool
    {
        return isset($this->previewItemList[$offset]);
    }

    public function jsonSerialize(): array
    {
        return $this->previewItemList;
    }
}

class_alias(PreviewItemList::class, 'Ibexa\Platform\Personalization\Value\Recommendation\PreviewItemList');
