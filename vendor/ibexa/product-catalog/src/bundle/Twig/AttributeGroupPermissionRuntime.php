<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Twig;

use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeGroup\AttributeGroupCreateStruct;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeGroup\Create;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeGroup\Delete;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeGroup\PreEdit;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Twig\Extension\RuntimeExtensionInterface;

final class AttributeGroupPermissionRuntime implements RuntimeExtensionInterface
{
    private PermissionResolverInterface $permissionResolver;

    public function __construct(
        PermissionResolverInterface $permissionResolver
    ) {
        $this->permissionResolver = $permissionResolver;
    }

    public function canCreateAttributeGroup(?AttributeGroupCreateStruct $attributeGroupCreateStruct = null): bool
    {
        return $this->permissionResolver->canUser(new Create($attributeGroupCreateStruct));
    }

    public function canEditAttributeGroup(?AttributeGroupInterface $attributeGroup = null): bool
    {
        return $this->permissionResolver->canUser(new PreEdit($attributeGroup));
    }

    public function canDeleteAttributeGroup(?AttributeGroupInterface $attributeGroup = null): bool
    {
        return $this->permissionResolver->canUser(new Delete($attributeGroup));
    }
}
