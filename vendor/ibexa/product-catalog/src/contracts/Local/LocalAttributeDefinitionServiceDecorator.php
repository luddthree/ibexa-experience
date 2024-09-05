<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local;

use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeDefinition\AttributeDefinitionCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeDefinition\AttributeDefinitionUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionListInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionQuery;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;

abstract class LocalAttributeDefinitionServiceDecorator implements LocalAttributeDefinitionServiceInterface
{
    protected LocalAttributeDefinitionServiceInterface $innerService;

    public function __construct(LocalAttributeDefinitionServiceInterface $innerService)
    {
        $this->innerService = $innerService;
    }

    public function getAttributeDefinition(
        string $identifier,
        ?iterable $prioritizedLanguages = null
    ): AttributeDefinitionInterface {
        return $this->innerService->getAttributeDefinition($identifier, $prioritizedLanguages);
    }

    public function findAttributesDefinitions(
        ?AttributeDefinitionQuery $query = null
    ): AttributeDefinitionListInterface {
        return $this->innerService->findAttributesDefinitions($query);
    }

    public function createAttributeDefinition(
        AttributeDefinitionCreateStruct $createStruct
    ): AttributeDefinitionInterface {
        return $this->innerService->createAttributeDefinition($createStruct);
    }

    public function deleteAttributeDefinition(AttributeDefinitionInterface $attributeDefinition): void
    {
        $this->innerService->deleteAttributeDefinition($attributeDefinition);
    }

    public function newAttributeDefinitionCreateStruct(string $identifier): AttributeDefinitionCreateStruct
    {
        return $this->innerService->newAttributeDefinitionCreateStruct($identifier);
    }

    public function newAttributeDefinitionUpdateStruct(
        AttributeDefinitionInterface $attributeDefinition
    ): AttributeDefinitionUpdateStruct {
        return $this->innerService->newAttributeDefinitionUpdateStruct($attributeDefinition);
    }

    public function updateAttributeDefinition(
        AttributeDefinitionInterface $attributeDefinition,
        AttributeDefinitionUpdateStruct $updateStruct
    ): AttributeDefinitionInterface {
        return $this->innerService->updateAttributeDefinition($attributeDefinition, $updateStruct);
    }

    public function deleteAttributeDefinitionTranslation(
        AttributeDefinitionInterface $attributeDefinition,
        string $languageCode
    ): void {
        $this->innerService->deleteAttributeDefinitionTranslation($attributeDefinition, $languageCode);
    }

    public function isAttributeDefinitionUsed(
        AttributeDefinitionInterface $attributeDefinition
    ): bool {
        return $this->innerService->isAttributeDefinitionUsed($attributeDefinition);
    }
}
