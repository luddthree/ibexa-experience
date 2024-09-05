<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Values\Product;

final class ProductVariantCreateStruct
{
    /** @var array<int|string,mixed> */
    private array $attributes;

    private ?string $code;

    /**
     * @param array<int|string,mixed> $attributes
     */
    public function __construct(array $attributes, ?string $code = null)
    {
        $this->code = $code;
        $this->attributes = $attributes;
    }

    /**
     * @return array<int|string,mixed>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param string $identifier
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    public function getAttribute(string $identifier, $default = null)
    {
        return $this->attributes[$identifier] ?? $default;
    }

    /**
     * @param mixed $value
     */
    public function setAttribute(string $identifier, $value): void
    {
        $this->attributes[$identifier] = $value;
    }

    /**
     * @param array<string,mixed> $attributes
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
