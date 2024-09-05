<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\ProductVariant;

use Ibexa\Contracts\Migration\StepExecutor\AbstractStepExecutor;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductVariantCreateStruct;
use Ibexa\Migration\ValueObject\Step\StepInterface;

final class ProductVariantCreateStepExecutor extends AbstractStepExecutor
{
    private LocalProductServiceInterface $productService;

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface $productService
     */
    public function __construct(LocalProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @param \Ibexa\ProductCatalog\Migrations\ProductVariant\ProductVariantCreateStep $step
     */
    protected function doHandle(StepInterface $step)
    {
        assert($step instanceof ProductVariantCreateStep);

        $product = $this->productService->getProduct($step->getBaseProductCode());

        $createStructs = [];
        foreach ($step->getVariants() as $variant) {
            $createStructs[] = new ProductVariantCreateStruct(
                $variant->getAttributes(),
                $variant->getCode()
            );
        }

        $this->productService->createProductVariants($product, $createStructs);

        return null;
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof ProductVariantCreateStep;
    }
}
