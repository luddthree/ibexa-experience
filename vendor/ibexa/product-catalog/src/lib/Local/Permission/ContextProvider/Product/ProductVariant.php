<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Permission\ContextProvider\Product;

use Ibexa\Contracts\ProductCatalog\Permission\ContextInterface;
use Ibexa\Contracts\ProductCatalog\Permission\ContextProvider\ContextProviderInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;
use Ibexa\ProductCatalog\Local\Permission\Context;
use Ibexa\ProductCatalog\Local\Repository\Values\ProductVariant as LocalProductVariant;

final class ProductVariant implements ContextProviderInterface
{
    public function accept(PolicyInterface $policy): bool
    {
        return $policy->getObject() instanceof LocalProductVariant;
    }

    public function getPermissionContext(PolicyInterface $policy): ContextInterface
    {
        /** @var \Ibexa\ProductCatalog\Local\Repository\Values\ProductVariant $productVariant */
        $productVariant = $policy->getObject();
        /** @var \Ibexa\ProductCatalog\Local\Repository\Values\Product $baseProduct */
        $baseProduct = $productVariant->getBaseProduct();

        return new Context($baseProduct->getContent());
    }
}
