<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Twig;

use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\Delete;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\PreEdit;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Twig\Extension\RuntimeExtensionInterface;

final class ProductPermissionRuntime implements RuntimeExtensionInterface
{
    private PermissionResolverInterface $permissionResolver;

    public function __construct(
        PermissionResolverInterface $permissionResolver
    ) {
        $this->permissionResolver = $permissionResolver;
    }

    public function canDeleteProduct(?ProductInterface $product = null): bool
    {
        return $this->permissionResolver->canUser(new Delete($product));
    }

    public function canEditProduct(?ProductInterface $product = null): bool
    {
        return $this->permissionResolver->canUser(new PreEdit($product));
    }
}
