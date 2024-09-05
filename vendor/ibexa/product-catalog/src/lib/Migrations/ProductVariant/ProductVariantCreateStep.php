<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\ProductVariant;

use Ibexa\Migration\ValueObject\Step\StepInterface;

final class ProductVariantCreateStep implements StepInterface
{
    private string $baseProductCode;

    /** @var \Ibexa\ProductCatalog\Migrations\ProductVariant\ProductVariantCreateStepEntry[] */
    private array $variants;

    /**
     * @param \Ibexa\ProductCatalog\Migrations\ProductVariant\ProductVariantCreateStepEntry[] $variants
     */
    public function __construct(string $baseProductCode, array $variants)
    {
        $this->baseProductCode = $baseProductCode;
        $this->variants = $variants;
    }

    public function getBaseProductCode(): string
    {
        return $this->baseProductCode;
    }

    /**
     * @return \Ibexa\ProductCatalog\Migrations\ProductVariant\ProductVariantCreateStepEntry[]
     */
    public function getVariants(): array
    {
        return $this->variants;
    }
}
