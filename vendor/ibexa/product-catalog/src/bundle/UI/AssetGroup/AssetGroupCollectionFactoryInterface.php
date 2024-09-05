<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\AssetGroup;

use Ibexa\Bundle\ProductCatalog\UI\AssetGroup\Values\AssetGroupCollection;

interface AssetGroupCollectionFactoryInterface
{
    /**
     * @param iterable<array-key, \Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface> $assets
     */
    public function createFromAssetCollection(
        iterable $assets
    ): AssetGroupCollection;
}
