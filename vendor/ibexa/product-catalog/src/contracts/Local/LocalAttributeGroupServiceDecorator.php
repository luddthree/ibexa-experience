<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local;

use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeGroup\AttributeGroupCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeGroup\AttributeGroupUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupListInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupQuery;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;

abstract class LocalAttributeGroupServiceDecorator implements LocalAttributeGroupServiceInterface
{
    protected LocalAttributeGroupServiceInterface $innerService;

    public function __construct(LocalAttributeGroupServiceInterface $innerService)
    {
        $this->innerService = $innerService;
    }

    /**
     * @param iterable<\Ibexa\Contracts\Core\Repository\Values\Content\Language>|null $prioritizedLanguages
     */
    public function getAttributeGroup(string $identifier, ?iterable $prioritizedLanguages = null): AttributeGroupInterface
    {
        return $this->innerService->getAttributeGroup($identifier, $prioritizedLanguages);
    }

    public function findAttributeGroups(?AttributeGroupQuery $query = null): AttributeGroupListInterface
    {
        return $this->innerService->findAttributeGroups($query);
    }

    public function createAttributeGroup(AttributeGroupCreateStruct $createStruct): AttributeGroupInterface
    {
        return $this->innerService->createAttributeGroup($createStruct);
    }

    public function deleteAttributeGroup(AttributeGroupInterface $group): void
    {
        $this->innerService->deleteAttributeGroup($group);
    }

    public function newAttributeGroupCreateStruct(string $identifier): AttributeGroupCreateStruct
    {
        return $this->innerService->newAttributeGroupCreateStruct($identifier);
    }

    public function newAttributeGroupUpdateStruct(AttributeGroupInterface $group): AttributeGroupUpdateStruct
    {
        return $this->innerService->newAttributeGroupUpdateStruct($group);
    }

    public function updateAttributeGroup(
        AttributeGroupInterface $attributeGroup,
        AttributeGroupUpdateStruct $updateStruct
    ): AttributeGroupInterface {
        return $this->innerService->updateAttributeGroup($attributeGroup, $updateStruct);
    }

    public function deleteAttributeGroupTranslation(
        AttributeGroupInterface $attributeGroup,
        string $languageCode
    ): void {
        $this->innerService->deleteAttributeGroupTranslation($attributeGroup, $languageCode);
    }

    public function isAttributeGroupUsed(
        AttributeGroupInterface $attributeGroup
    ): bool {
        return $this->innerService->isAttributeGroupUsed($attributeGroup);
    }
}
