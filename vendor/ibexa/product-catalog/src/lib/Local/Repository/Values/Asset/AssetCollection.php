<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Values\Asset;

use Ibexa\Contracts\Core\Collection\ArrayList;
use Ibexa\Contracts\ProductCatalog\Values\Asset\AssetCollectionInterface;
use Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface;

/**
 * @template-extends \Ibexa\Contracts\Core\Collection\ArrayList<
 *     \Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface
 * >
 */
final class AssetCollection extends ArrayList implements AssetCollectionInterface
{
    public function withTag(string $tag, ?string $value = null): AssetCollectionInterface
    {
        return $this->withTags([$tag => $value]);
    }

    /**
     * @param array<string, string|null> $tags
     */
    public function withTags(array $tags): AssetCollectionInterface
    {
        $predicate = static fn (AssetInterface $asset) => !empty(array_intersect_assoc($asset->getTags(), $tags));

        return $this->filter($predicate);
    }
}
