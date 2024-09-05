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
 * @implements IteratorAggregate<array-key, \Ibexa\Personalization\Value\Model\EditorContent>
 * @implements ArrayAccess<array-key, \Ibexa\Personalization\Value\Model\EditorContent>
 */
final class EditorContentList implements IteratorAggregate, ArrayAccess, JsonSerializable, Countable
{
    /** @var \Ibexa\Personalization\Value\Model\EditorContent[] */
    private $editorContentList;

    public function __construct(array $editorContentList = [])
    {
        foreach ($editorContentList as $editorContent) {
            if (false === $editorContent instanceof EditorContent) {
                throw new InvalidArgumentException(
                    '$editorContent',
                    sprintf(
                        'Must be of type: %s, %s given',
                        EditorContent::class,
                        is_object($editorContent) ? get_class($editorContent) : gettype($editorContent)
                    )
                );
            }
        }
        $this->editorContentList = $editorContentList;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->editorContentList);
    }

    public function offsetUnset($offset): void
    {
        unset($this->editorContentList[$offset]);
    }

    public function offsetSet($offset, $value): void
    {
        throw new BadMethodCallException('Unsupported method');
    }

    public function offsetGet($offset): EditorContent
    {
        return $this->editorContentList[$offset];
    }

    public function offsetExists($offset): bool
    {
        return isset($this->editorContentList[$offset]);
    }

    public function count(): int
    {
        return count($this->editorContentList);
    }

    public function slice(int $offset, int $length): array
    {
        return array_slice($this->editorContentList, $offset, $length);
    }

    public function jsonSerialize(): array
    {
        return $this->editorContentList;
    }
}

class_alias(EditorContentList::class, 'Ibexa\Platform\Personalization\Value\Model\EditorContentList');
