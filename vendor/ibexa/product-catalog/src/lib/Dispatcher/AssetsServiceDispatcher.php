<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Dispatcher;

use Ibexa\Contracts\ProductCatalog\AssetServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Asset\AssetCollectionInterface;
use Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

/**
 * @extends \Ibexa\ProductCatalog\Dispatcher\AbstractServiceDispatcher<
 *     \Ibexa\Contracts\ProductCatalog\AssetServiceInterface
 * >
 */
final class AssetsServiceDispatcher extends AbstractServiceDispatcher implements AssetServiceInterface
{
    public function findAssets(ProductInterface $product): AssetCollectionInterface
    {
        return $this->dispatch()->findAssets($product);
    }

    public function getAsset(ProductInterface $product, string $identifier): AssetInterface
    {
        return $this->dispatch()->getAsset($product, $identifier);
    }
}
