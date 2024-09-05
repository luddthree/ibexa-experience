<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Permission\ContextProvider\Product;

use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductCreateStruct as LocalProductCreateStruct;
use Ibexa\Contracts\ProductCatalog\Permission\ContextInterface;
use Ibexa\Contracts\ProductCatalog\Permission\ContextProvider\ContextProviderInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;
use Ibexa\ProductCatalog\Local\Permission\Context;

final class ProductCreateStruct implements ContextProviderInterface
{
    public function accept(PolicyInterface $policy): bool
    {
        return $policy->getObject() instanceof LocalProductCreateStruct;
    }

    public function getPermissionContext(PolicyInterface $policy): ContextInterface
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductCreateStruct $productCreateStruct */
        $productCreateStruct = $policy->getObject();

        return new Context($productCreateStruct->getContentCreateStruct());
    }
}
