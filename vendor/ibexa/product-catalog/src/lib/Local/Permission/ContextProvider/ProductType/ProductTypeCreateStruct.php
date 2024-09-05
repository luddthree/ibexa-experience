<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Permission\ContextProvider\ProductType;

use Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\ProductTypeCreateStruct as LocalProductTypeCreateStruct;
use Ibexa\Contracts\ProductCatalog\Permission\ContextInterface;
use Ibexa\Contracts\ProductCatalog\Permission\ContextProvider\ContextProviderInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;
use Ibexa\ProductCatalog\Local\Permission\Context;

final class ProductTypeCreateStruct implements ContextProviderInterface
{
    public function accept(PolicyInterface $policy): bool
    {
        return $policy->getObject() instanceof LocalProductTypeCreateStruct;
    }

    public function getPermissionContext(PolicyInterface $policy): ContextInterface
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\ProductTypeCreateStruct $productTypeCreateStruct */
        $productTypeCreateStruct = $policy->getObject();

        return new Context($productTypeCreateStruct->getContentTypeCreateStruct());
    }
}
