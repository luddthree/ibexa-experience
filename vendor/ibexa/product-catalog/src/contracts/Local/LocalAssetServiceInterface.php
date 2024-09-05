<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local;

use Ibexa\Contracts\ProductCatalog\AssetServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\Asset\AssetCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\Asset\AssetUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

interface LocalAssetServiceInterface extends AssetServiceInterface
{
    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function createAsset(ProductInterface $product, AssetCreateStruct $createStruct): AssetInterface;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function deleteAsset(ProductInterface $product, AssetInterface $asset): void;

    public function newAssetCreateStruct(): AssetCreateStruct;

    public function newAssetUpdateStruct(): AssetUpdateStruct;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function updateAsset(
        ProductInterface $product,
        AssetInterface $asset,
        AssetUpdateStruct $updateStruct
    ): AssetInterface;
}
