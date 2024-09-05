<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Twig\Test;

use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Twig\Extension\RuntimeExtensionInterface;

final class AttributeDefinitionUsedRuntime implements RuntimeExtensionInterface
{
    private LocalAttributeDefinitionServiceInterface $attributeDefinitionService;

    public function __construct(
        LocalAttributeDefinitionServiceInterface $attributeDefinitionService
    ) {
        $this->attributeDefinitionService = $attributeDefinitionService;
    }

    public function attributeDefinitionUsed(AttributeDefinitionInterface $attribute): bool
    {
        return $this->attributeDefinitionService->isAttributeDefinitionUsed($attribute);
    }
}
