<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\ProductAsset;

use Ibexa\Contracts\Migration\StepExecutor\AbstractStepExecutor;
use Ibexa\Contracts\ProductCatalog\Local\LocalAssetServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\Asset\AssetCreateStruct;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;

final class ProductAssetCreateStepExecutor extends AbstractStepExecutor
{
    private ProductServiceInterface $productService;

    private LocalAssetServiceInterface $assetService;

    public function __construct(LocalAssetServiceInterface $assetService, ProductServiceInterface $productService)
    {
        $this->assetService = $assetService;
        $this->productService = $productService;
    }

    /**
     * @param \Ibexa\ProductCatalog\Migrations\ProductAsset\ProductAssetCreateStep $step
     */
    protected function doHandle(StepInterface $step)
    {
        assert($step instanceof ProductAssetCreateStep);

        $product = $this->productService->getProduct($step->getProductCode());

        $createStruct = new AssetCreateStruct();
        $createStruct->setUri($step->getUri());
        $createStruct->setTags($step->getTags());

        $this->assetService->createAsset($product, $createStruct);

        return null;
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof ProductAssetCreateStep;
    }
}
