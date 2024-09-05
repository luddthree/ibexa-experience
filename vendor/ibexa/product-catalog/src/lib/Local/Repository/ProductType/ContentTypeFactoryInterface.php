<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\ProductType;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeCreateStruct;

interface ContentTypeFactoryInterface
{
    /**
     * @param iterable<int, \Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\AssignAttributeDefinitionStruct> $attributes
     */
    public function createContentTypeCreateStruct(
        string $identifier,
        string $mainLanguageCode,
        iterable $attributes = [],
        bool $isVirtual = false
    ): ContentTypeCreateStruct;
}
