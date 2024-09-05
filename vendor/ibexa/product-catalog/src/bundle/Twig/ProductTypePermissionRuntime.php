<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Twig;

use Ibexa\Contracts\ProductCatalog\Permission\Policy\ProductType\Create;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\ProductType\Delete;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\ProductType\PreEdit;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Twig\Extension\RuntimeExtensionInterface;

final class ProductTypePermissionRuntime implements RuntimeExtensionInterface
{
    private PermissionResolverInterface $permissionResolver;

    public function __construct(
        PermissionResolverInterface $permissionResolver
    ) {
        $this->permissionResolver = $permissionResolver;
    }

    public function canDeleteProductType(?ProductTypeInterface $productType = null): bool
    {
        return $this->permissionResolver->canUser(new Delete($productType));
    }

    public function canCreateProductType(): bool
    {
        return $this->permissionResolver->canUser(new Create());
    }

    public function canEditProductType(?ProductTypeInterface $productType = null): bool
    {
        return $this->permissionResolver->canUser(new PreEdit($productType));
    }
}
