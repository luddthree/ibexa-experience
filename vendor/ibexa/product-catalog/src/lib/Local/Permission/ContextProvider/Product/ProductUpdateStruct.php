<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Permission\ContextProvider\Product;

use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductUpdateStruct as LocalProductUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Permission\ContextInterface;
use Ibexa\Contracts\ProductCatalog\Permission\ContextProvider\ContextProviderInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;
use Ibexa\ProductCatalog\Local\Permission\Context;

final class ProductUpdateStruct implements ContextProviderInterface
{
    public function accept(PolicyInterface $policy): bool
    {
        return $policy->getObject() instanceof LocalProductUpdateStruct;
    }

    public function getPermissionContext(PolicyInterface $policy): ContextInterface
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductUpdateStruct $productUpdateStruct */
        $productUpdateStruct = $policy->getObject();

        return new Context($productUpdateStruct->getContentUpdateStruct());
    }
}
