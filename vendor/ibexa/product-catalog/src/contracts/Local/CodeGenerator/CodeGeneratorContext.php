<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\CodeGenerator;

use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;

final class CodeGeneratorContext
{
    private ProductTypeInterface $type;

    /** @var array<string,mixed> */
    private array $attributes;

    private ?ProductInterface $baseProduct;

    private ?int $index;

    /**
     * @param array<string,mixed> $attributes
     */
    public function __construct(
        ProductTypeInterface $type,
        array $attributes = [],
        ?ProductInterface $baseProduct = null,
        ?int $index = null
    ) {
        $this->type = $type;
        $this->attributes = $attributes;
        $this->baseProduct = $baseProduct;
        $this->index = $index;
    }

    public function getType(): ProductTypeInterface
    {
        return $this->type;
    }

    /**
     * @return array<string,mixed>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param array<string,mixed> $attributes
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function hasBaseProduct(): bool
    {
        return $this->baseProduct !== null;
    }

    public function getBaseProduct(): ?ProductInterface
    {
        return $this->baseProduct;
    }

    public function setBaseProduct(?ProductInterface $baseProduct): void
    {
        $this->baseProduct = $baseProduct;
    }

    public function getIndex(): ?int
    {
        return $this->index;
    }

    public function setIndex(?int $index): void
    {
        $this->index = $index;
    }

    public function hasIndex(): bool
    {
        return $this->index !== null;
    }
}
