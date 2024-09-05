<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Personalization\Product;

use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;

final class BaseProductDataProvider implements DataProviderInterface
{
    private const DATA_KEY = 'product_is_base';

    public function getData(ContentAwareProductInterface $product, string $languageCode): ?array
    {
        if ($product->isBaseProduct()) {
            return [
                self::DATA_KEY => true,
            ];
        }

        return null;
    }
}
