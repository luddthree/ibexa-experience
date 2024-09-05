<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\ProductVariant;

final class ProductVariantCreateStepEntry
{
    /** @var array<int|string,mixed> */
    private array $attributes;

    private ?string $code;

    /**
     * @param array<int|string,mixed> $attributes
     */
    public function __construct(array $attributes, ?string $code = null)
    {
        $this->attributes = $attributes;
        $this->code = $code;
    }

    /**
     * @return array<int|string,mixed>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }
}
