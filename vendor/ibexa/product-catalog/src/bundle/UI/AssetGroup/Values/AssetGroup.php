<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\AssetGroup\Values;

use Ibexa\Contracts\ProductCatalog\Values\Asset\AssetCollectionInterface;
use IteratorAggregate;
use IteratorIterator;

/**
 * @template-implements \IteratorAggregate<\Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface>
 */
final class AssetGroup implements IteratorAggregate
{
    /** @var \Ibexa\Bundle\ProductCatalog\UI\AssetGroup\Values\Tag[] */
    private array $tags;

    private AssetCollectionInterface $assets;

    /**
     * @param \Ibexa\Bundle\ProductCatalog\UI\AssetGroup\Values\Tag[] $tags
     */
    public function __construct(array $tags, AssetCollectionInterface $assets)
    {
        $this->tags = $tags;
        $this->assets = $assets;
    }

    /**
     * @return \Ibexa\Bundle\ProductCatalog\UI\AssetGroup\Values\Tag[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    public function getAssets(): AssetCollectionInterface
    {
        return $this->assets;
    }

    public function isBaseProductGroup(): bool
    {
        return $this->tags === [];
    }

    /**
     * @phpstan-return \IteratorIterator<
     *     array-key,
     *     \Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface,
     *     \Ibexa\Contracts\ProductCatalog\Values\Asset\AssetCollectionInterface
     * >
     */
    public function getIterator(): IteratorIterator
    {
        return new IteratorIterator($this->assets);
    }
}
