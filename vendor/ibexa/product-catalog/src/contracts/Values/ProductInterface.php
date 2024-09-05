<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values;

use DateTimeInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Thumbnail;
use Ibexa\Contracts\ProductCatalog\Values\Asset\AssetCollectionInterface;

interface ProductInterface
{
    public function getCode(): string;

    public function getName(): string;

    public function getProductType(): ProductTypeInterface;

    public function getThumbnail(): ?Thumbnail;

    public function getAssets(): AssetCollectionInterface;

    public function getCreatedAt(): DateTimeInterface;

    public function getUpdatedAt(): DateTimeInterface;

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\AttributeInterface[]
     */
    public function getAttributes(): iterable;

    public function isBaseProduct(): bool;

    public function isVariant(): bool;
}
