<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Personalization\Product;

use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;

interface DataResolverInterface
{
    /**
     * @return array<string, scalar|array<scalar|null>|null>
     */
    public function resolve(ContentAwareProductInterface $product, string $languageCode): array;
}
