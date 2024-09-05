<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\AssetTags;

/**
 * @template TValue
 * @template TStorageValue
 */
interface AssetTagsStorageConverterInterface
{
    /**
     * @param TValue $tag
     *
     * @return TStorageValue
     */
    public function convertToStorage($tag);

    /**
     * @param TStorageValue $tag
     *
     * @return TValue
     */
    public function convertFromStorage($tag);

    /**
     * @param TValue $value
     */
    public function supportsToStorage(string $attributeTypeIdentifier, $value): bool;

    /**
     * @param TStorageValue $value
     */
    public function supportsFromStorage(string $attributeTypeIdentifier, $value): bool;
}
