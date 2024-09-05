<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local;

use Ibexa\Contracts\ProductCatalog\Local\Values\Asset\AssetCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\Asset\AssetUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Asset\AssetCollectionInterface;
use Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

abstract class LocalAssetServiceDecorator implements LocalAssetServiceInterface
{
    protected LocalAssetServiceInterface $innerService;

    public function __construct(LocalAssetServiceInterface $innerService)
    {
        $this->innerService = $innerService;
    }

    public function findAssets(ProductInterface $product): AssetCollectionInterface
    {
        return $this->innerService->findAssets($product);
    }

    public function getAsset(ProductInterface $product, string $identifier): AssetInterface
    {
        return $this->innerService->getAsset($product, $identifier);
    }

    public function createAsset(ProductInterface $product, AssetCreateStruct $createStruct): AssetInterface
    {
        return $this->innerService->createAsset($product, $createStruct);
    }

    public function deleteAsset(ProductInterface $product, AssetInterface $asset): void
    {
        $this->innerService->deleteAsset($product, $asset);
    }

    public function newAssetCreateStruct(): AssetCreateStruct
    {
        return $this->innerService->newAssetCreateStruct();
    }

    public function newAssetUpdateStruct(): AssetUpdateStruct
    {
        return $this->innerService->newAssetUpdateStruct();
    }

    public function updateAsset(
        ProductInterface $product,
        AssetInterface $asset,
        AssetUpdateStruct $updateStruct
    ): AssetInterface {
        return $this->innerService->updateAsset($product, $asset, $updateStruct);
    }
}
