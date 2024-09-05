<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Twig\Test;

use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Twig\Extension\RuntimeExtensionInterface;

final class AttributeGroupUsedRuntime implements RuntimeExtensionInterface
{
    private LocalAttributeGroupServiceInterface $attributeGroupService;

    public function __construct(
        LocalAttributeGroupServiceInterface $attributeGroupService
    ) {
        $this->attributeGroupService = $attributeGroupService;
    }

    public function attributeGroupUsed(AttributeGroupInterface $attributeGroup): bool
    {
        return $this->attributeGroupService->isAttributeGroupUsed($attributeGroup);
    }
}
