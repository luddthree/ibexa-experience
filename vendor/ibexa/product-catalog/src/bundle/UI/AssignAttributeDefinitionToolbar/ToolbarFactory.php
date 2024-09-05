<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\AssignAttributeDefinitionToolbar;

use Ibexa\Bundle\ProductCatalog\UI\AssignAttributeDefinitionToolbar\Values\Toolbar;
use Ibexa\Bundle\ProductCatalog\UI\AssignAttributeDefinitionToolbar\Values\ToolbarGroup;
use Ibexa\Bundle\ProductCatalog\UI\AssignAttributeDefinitionToolbar\Values\ToolbarGroupItem;
use Ibexa\Contracts\Core\Repository\Iterator\BatchIterator;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Iterator\BatchIteratorAdapter\AttributeDefinitionFetchAdapter;
use Ibexa\Contracts\ProductCatalog\Iterator\BatchIteratorAdapter\AttributeGroupFetchAdapter;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionQuery;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\Query\AttributeGroupIdentifierCriterion;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;

final class ToolbarFactory implements ToolbarFactoryInterface
{
    private AttributeGroupServiceInterface $attributeGroupService;

    private AttributeDefinitionServiceInterface $attributeDefinitionService;

    public function __construct(
        AttributeGroupServiceInterface $attributeGroupService,
        AttributeDefinitionServiceInterface $attributeDefinitionService
    ) {
        $this->attributeDefinitionService = $attributeDefinitionService;
        $this->attributeGroupService = $attributeGroupService;
    }

    public function create(?ProductTypeInterface $productType = null): Toolbar
    {
        return new Toolbar($this->createToolbarGroups($productType));
    }

    /**
     * @return \Ibexa\Bundle\ProductCatalog\UI\AssignAttributeDefinitionToolbar\Values\ToolbarGroup[]
     */
    private function createToolbarGroups(?ProductTypeInterface $productType): array
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface[] $groups */
        $groups = new BatchIterator(new AttributeGroupFetchAdapter($this->attributeGroupService));

        $items = [];
        foreach ($groups as $group) {
            $groupItems = $this->createToolbarGroupItems($productType, $group);

            if (!empty($groupItems)) {
                $items[] = new ToolbarGroup(
                    $group->getIdentifier(),
                    $group->getName(),
                    $groupItems
                );
            }
        }

        return $items;
    }

    /**
     * @return \Ibexa\Bundle\ProductCatalog\UI\AssignAttributeDefinitionToolbar\Values\ToolbarGroupItem[]
     */
    private function createToolbarGroupItems(?ProductTypeInterface $productType, AttributeGroupInterface $group): array
    {
        $query = new AttributeDefinitionQuery(
            new AttributeGroupIdentifierCriterion($group->getIdentifier()),
        );
        $attributesDefinitions = new BatchIterator(new AttributeDefinitionFetchAdapter(
            $this->attributeDefinitionService,
            $query
        ));

        $items = [];
        /** @var \Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface $attributeDefinition */
        foreach ($attributesDefinitions as $attributeDefinition) {
            $items[] = new ToolbarGroupItem(
                $attributeDefinition->getIdentifier(),
                $attributeDefinition->getName(),
                $attributeDefinition->getType()->getIdentifier(),
                $this->isAssignedToProductType($productType, $attributeDefinition)
            );
        }

        return $items;
    }

    private function isAssignedToProductType(
        ?ProductTypeInterface $productType,
        AttributeDefinitionInterface $attributeDefinition
    ): bool {
        if ($productType === null) {
            return false;
        }

        foreach ($productType->getAttributesDefinitions() as $assignment) {
            if ($assignment->getAttributeDefinition()->getIdentifier() === $attributeDefinition->getIdentifier()) {
                return true;
            }
        }

        return false;
    }
}
