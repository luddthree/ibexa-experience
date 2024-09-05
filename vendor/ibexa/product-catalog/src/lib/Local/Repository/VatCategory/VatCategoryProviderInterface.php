<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\VatCategory;

use Ibexa\Contracts\ProductCatalog\Values\VatCategoryInterface;

interface VatCategoryProviderInterface
{
    public function getVatCategory(string $region, string $identifier): VatCategoryInterface;

    /**
     * @return array<string,\Ibexa\Contracts\ProductCatalog\Values\VatCategoryInterface>
     */
    public function getVatCategories(string $region): array;
}
