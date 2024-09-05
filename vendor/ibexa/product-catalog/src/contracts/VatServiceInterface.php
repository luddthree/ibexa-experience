<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog;

use Ibexa\Contracts\ProductCatalog\Values\RegionInterface;
use Ibexa\Contracts\ProductCatalog\Values\VatCategory\VatCategoryListInterface;
use Ibexa\Contracts\ProductCatalog\Values\VatCategoryInterface;

interface VatServiceInterface
{
    public function getVatCategories(RegionInterface $region): VatCategoryListInterface;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function getVatCategoryByIdentifier(
        RegionInterface $region,
        string $identifier
    ): VatCategoryInterface;
}
