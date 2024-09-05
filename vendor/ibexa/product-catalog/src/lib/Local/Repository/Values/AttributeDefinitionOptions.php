<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Values;

use ArrayAccess;
use ArrayIterator;
use Ibexa\Contracts\Core\Options\OptionsBag;
use Iterator;
use IteratorAggregate;
use RuntimeException;

/**
 * @implements IteratorAggregate<string, mixed>
 * @implements ArrayAccess<string, mixed>
 */
final class AttributeDefinitionOptions implements OptionsBag, IteratorAggregate, ArrayAccess
{
    /** @var array<string,mixed> */
    private array $options;

    /**
     * @param array<string,mixed> $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * @return array<string,mixed>
     */
    public function all(): array
    {
        return $this->options;
    }

    /**
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    public function get(string $key, $default = null)
    {
        return $this->options[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return isset($this->options[$key]);
    }

    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->options);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->options[$offset]);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->options[$offset];
    }

    /**
     * @return never
     */
    public function offsetSet($offset, $value): void
    {
        throw new RuntimeException('Changing options is forbidden');
    }

    /**
     * @return never
     */
    public function offsetUnset($offset): void
    {
        throw new RuntimeException('Changing options is forbidden');
    }
}
