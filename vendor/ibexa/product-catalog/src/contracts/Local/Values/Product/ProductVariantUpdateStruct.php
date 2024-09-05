<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Values\Product;

final class ProductVariantUpdateStruct
{
    /** @var array<string, object|scalar|array<mixed>|null> */
    private array $attributes;

    private ?string $code;

    /**
     * @param array<string, object|scalar|array<mixed>|null> $attributes
     */
    public function __construct(array $attributes = [], ?string $code = null)
    {
        $this->attributes = $attributes;
        $this->code = $code;
    }

    /**
     * @return array<string, object|scalar|array<mixed>|null>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param object|scalar|array<mixed>|null $default
     *
     * @return object|scalar|array<mixed>|null
     */
    public function getAttribute(string $identifier, $default = null)
    {
        return $this->attributes[$identifier] ?? $default;
    }

    public function hasAttribute(string $identifier): bool
    {
        return isset($this->attributes[$identifier]);
    }

    public function hasAttributes(): bool
    {
        return !empty($this->attributes);
    }

    /**
     * @param object|scalar|array<mixed>|null $value
     */
    public function setAttribute(string $identifier, $value): void
    {
        $this->attributes[$identifier] = $value;
    }

    /**
     * @param array<string, object|scalar|array<mixed>|null> $attributes
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }
}
