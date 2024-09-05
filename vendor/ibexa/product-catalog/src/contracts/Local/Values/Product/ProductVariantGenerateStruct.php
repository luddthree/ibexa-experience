<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Values\Product;

final class ProductVariantGenerateStruct
{
    /** @var array<string, array<mixed>> */
    private array $attributes;

    /**
     * @param array<string, array<mixed>> $attributes
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param array<string, array<mixed>> $attributes
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    /**
     * @return array<mixed>|null
     */
    public function getAttribute(string $identifier): ?array
    {
        return $this->attributes[$identifier] ?? null;
    }

    /**
     * @param array<mixed> $value
     */
    public function setAttribute(string $identifier, array $value): void
    {
        $this->attributes[$identifier] = $value;
    }
}
