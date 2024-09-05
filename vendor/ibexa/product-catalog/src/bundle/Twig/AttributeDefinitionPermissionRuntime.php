<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Twig;

use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeDefinition\AttributeDefinitionCreateStruct;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeDefinition\Create;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeDefinition\Delete;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeDefinition\PreEdit;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Twig\Extension\RuntimeExtensionInterface;

final class AttributeDefinitionPermissionRuntime implements RuntimeExtensionInterface
{
    private PermissionResolverInterface $permissionResolver;

    public function __construct(
        PermissionResolverInterface $permissionResolver
    ) {
        $this->permissionResolver = $permissionResolver;
    }

    public function canCreateAttributeDefinition(?AttributeDefinitionCreateStruct $attributeDefinitionCreateStruct = null): bool
    {
        return $this->permissionResolver->canUser(new Create($attributeDefinitionCreateStruct));
    }

    public function canEditAttributeDefinition(?AttributeDefinitionInterface $attributeDefinition = null): bool
    {
        return $this->permissionResolver->canUser(new PreEdit($attributeDefinition));
    }

    public function canDeleteAttributeDefinition(?AttributeDefinitionInterface $attributeDefinition = null): bool
    {
        return $this->permissionResolver->canUser(new Delete($attributeDefinition));
    }
}
