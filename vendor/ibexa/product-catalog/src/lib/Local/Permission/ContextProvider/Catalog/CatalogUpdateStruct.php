<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Permission\ContextProvider\Catalog;

use Ibexa\Contracts\ProductCatalog\Permission\ContextInterface;
use Ibexa\Contracts\ProductCatalog\Permission\ContextProvider\ContextProviderInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogUpdateStruct as CatalogUpdateStructValue;
use Ibexa\ProductCatalog\Local\Permission\Context;

final class CatalogUpdateStruct implements ContextProviderInterface
{
    public function accept(PolicyInterface $policy): bool
    {
        return $policy->getObject() instanceof CatalogUpdateStructValue;
    }

    public function getPermissionContext(PolicyInterface $policy): ContextInterface
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogUpdateStruct $catalogUpdateStruct */
        $catalogUpdateStruct = $policy->getObject();

        return new Context($catalogUpdateStruct);
    }
}
