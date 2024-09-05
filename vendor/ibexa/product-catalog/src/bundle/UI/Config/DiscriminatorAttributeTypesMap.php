<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\Config;

use Ibexa\Bundle\ProductCatalog\Form\Attribute\VariantFormMapperRegistryInterface;
use Ibexa\Contracts\AdminUi\UI\Config\ProviderInterface;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;

final class DiscriminatorAttributeTypesMap implements ProviderInterface
{
    private VariantFormMapperRegistryInterface $formMapperRegistry;

    private AttributeDefinitionServiceInterface $attributeDefinitionService;

    public function __construct(
        VariantFormMapperRegistryInterface $formMapperRegistry,
        AttributeDefinitionServiceInterface $attributeDefinitionService
    ) {
        $this->formMapperRegistry = $formMapperRegistry;
        $this->attributeDefinitionService = $attributeDefinitionService;
    }

    /**
     * @return array<string, bool>
     */
    public function getConfig(): array
    {
        $attributeTypeDiscriminatorMap = [];
        foreach ($this->attributeDefinitionService->findAttributesDefinitions() as $definition) {
            $attributeTypeIdentifier = $definition->getType()->getIdentifier();
            $attributeTypeDiscriminatorMap[$attributeTypeIdentifier] = $this->formMapperRegistry->hasMapper(
                $attributeTypeIdentifier
            );
        }

        return $attributeTypeDiscriminatorMap;
    }
}
