<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Asset;

use Ibexa\Contracts\Core\Collection\ListInterface;
use Ibexa\Contracts\Core\Collection\StreamableInterface;

/**
 * @template-extends \Ibexa\Contracts\Core\Collection\ListInterface<
 *     \Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface
 * >
 * @template-extends \Ibexa\Contracts\Core\Collection\StreamableInterface<
 *     \Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface
 * >
 */
interface AssetCollectionInterface extends ListInterface, StreamableInterface
{
    public function withTag(string $tag, ?string $value = null): self;

    /**
     * @param array<string, string|null> $tags
     */
    public function withTags(array $tags): self;
}
