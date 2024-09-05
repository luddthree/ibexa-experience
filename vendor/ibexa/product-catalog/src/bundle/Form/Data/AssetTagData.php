<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

final class AssetTagData
{
    /** @var \Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface[]|null */
    private ?array $assets;

    /** @var \Ibexa\Bundle\ProductCatalog\Form\Data\AttributeData[]|null */
    private ?array $attributes;

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface[]|null $assets
     * @param \Ibexa\Bundle\ProductCatalog\Form\Data\AttributeData[]|null $attributes
     */
    public function __construct(?array $assets = null, ?array $attributes = null)
    {
        $this->assets = $assets;
        $this->attributes = $attributes;
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface[]|null
     */
    public function getAssets(): ?array
    {
        return $this->assets;
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface[]|null $assets
     */
    public function setAssets(?array $assets): void
    {
        $this->assets = $assets;
    }

    /**
     * @return \Ibexa\Bundle\ProductCatalog\Form\Data\AttributeData[]|null
     */
    public function getAttributes(): ?array
    {
        return $this->attributes;
    }

    /**
     * @param \Ibexa\Bundle\ProductCatalog\Form\Data\AttributeData[]|null $attributes
     */
    public function setAttributes(?array $attributes): void
    {
        $this->attributes = $attributes;
    }
}
