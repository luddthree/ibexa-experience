<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Twig\Test;

use Ibexa\Contracts\ProductCatalog\Local\LocalProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Twig\Extension\RuntimeExtensionInterface;

final class ProductTypeUsedRuntime implements RuntimeExtensionInterface
{
    private LocalProductTypeServiceInterface $productTypeService;

    public function __construct(
        LocalProductTypeServiceInterface $productTypeService
    ) {
        $this->productTypeService = $productTypeService;
    }

    public function productTypeUsed(ProductTypeInterface $productType): bool
    {
        return $this->productTypeService->isProductTypeUsed($productType);
    }
}
