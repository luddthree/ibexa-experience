<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

final class AssetBulkDeleteData
{
    /** @var \Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface[] */
    private array $assets;

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface[] $assets
     */
    public function __construct(array $assets = [])
    {
        $this->assets = $assets;
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface[]
     */
    public function getAssets(): array
    {
        return $this->assets;
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface[] $assets
     */
    public function setAssets(array $assets): void
    {
        $this->assets = $assets;
    }
}
