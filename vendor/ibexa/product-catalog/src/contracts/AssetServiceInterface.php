<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog;

use Ibexa\Contracts\ProductCatalog\Values\Asset\AssetCollectionInterface;
use Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

interface AssetServiceInterface
{
    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function findAssets(ProductInterface $product): AssetCollectionInterface;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function getAsset(ProductInterface $product, string $identifier): AssetInterface;
}
