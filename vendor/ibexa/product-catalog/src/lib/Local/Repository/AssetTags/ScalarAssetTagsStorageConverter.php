<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\AssetTags;

/**
 * @internal
 *
 * @implements \Ibexa\ProductCatalog\Local\Repository\AssetTags\AssetTagsStorageConverterInterface<scalar, scalar>
 */
final class ScalarAssetTagsStorageConverter implements AssetTagsStorageConverterInterface
{
    public function convertToStorage($tag)
    {
        return $tag;
    }

    public function convertFromStorage($tag)
    {
        return $tag;
    }

    /**
     * @param scalar $value
     */
    public function supportsToStorage(string $attributeTypeIdentifier, $value): bool
    {
        return is_scalar($value);
    }

    /**
     * @param scalar $value
     */
    public function supportsFromStorage(string $attributeTypeIdentifier, $value): bool
    {
        return is_scalar($value);
    }
}
