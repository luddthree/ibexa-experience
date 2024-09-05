<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\UI\AssignAttributeDefinitionToolbar;

use ArrayIterator;
use Ibexa\Bundle\ProductCatalog\UI\AssignAttributeDefinitionToolbar\ToolbarFactory;
use Ibexa\Bundle\ProductCatalog\UI\AssignAttributeDefinitionToolbar\Values\Toolbar;
use Ibexa\Bundle\ProductCatalog\UI\AssignAttributeDefinitionToolbar\Values\ToolbarGroup;
use Ibexa\Bundle\ProductCatalog\UI\AssignAttributeDefinitionToolbar\Values\ToolbarGroupItem;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionListInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionAssignmentInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupListInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupQuery;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use PHPUnit\Framework\TestCase;

final class ToolbarFactoryTest extends TestCase
{
    /** @var \Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private AttributeGroupServiceInterface $attributeGroupService;

    /** @var \Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private AttributeDefinitionServiceInterface $attributeDefinitionService;

    private ToolbarFactory $toolbarFactory;

    protected function setUp(): void
    {
        $this->attributeGroupService = $this->createMock(AttributeGroupServiceInterface::class);
        $this->attributeDefinitionService = $this->createMock(AttributeDefinitionServiceInterface::class);

        $this->toolbarFactory = new ToolbarFactory(
            $this->attributeGroupService,
            $this->attributeDefinitionService
        );
    }

    public function testCreate(): void
    {
        $this->configureExampleData();

        self::assertEquals(
            new Toolbar([
                new ToolbarGroup('group_a', 'Group A', [
                    new ToolbarGroupItem('foo', 'Foo', 'string'),
                    new ToolbarGroupItem('bar', 'Bar', 'integer'),
                    new ToolbarGroupItem('baz', 'Baz', 'boolean'),
                ]),
                new ToolbarGroup('group_c', 'Group C', [
                    new ToolbarGroupItem('foobar', 'Foobar', 'string'),
                ]),
            ]),
            $this->toolbarFactory->create()
        );
    }

    public function testCreateForExistingProductType(): void
    {
        $this->configureExampleData();

        $productType = $this->createProductTypeWithAttributes(['bar', 'baz']);

        self::assertEquals(
            new Toolbar([
                new ToolbarGroup('group_a', 'Group A', [
                    new ToolbarGroupItem('foo', 'Foo', 'string'),
                    new ToolbarGroupItem('bar', 'Bar', 'integer', true),
                    new ToolbarGroupItem('baz', 'Baz', 'boolean', true),
                ]),
                new ToolbarGroup('group_c', 'Group C', [
                    new ToolbarGroupItem('foobar', 'Foobar', 'string'),
                ]),
            ]),
            $this->toolbarFactory->create($productType)
        );
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface[] $groups
     */
    private function createAttributeGroupList(array $groups, int $totalCount): AttributeGroupListInterface
    {
        $list = $this->createMock(AttributeGroupListInterface::class);
        $list->method('getTotalCount')->willReturn($totalCount);
        $list->method('getAttributeGroups')->willReturn($groups);
        $list->method('getIterator')->willReturn(new ArrayIterator($groups));

        return $list;
    }

    private function createAttributeGroup(string $identifier, string $name): AttributeGroupInterface
    {
        $group = $this->createMock(AttributeGroupInterface::class);
        $group->method('getIdentifier')->willReturn($identifier);
        $group->method('getName')->willReturn($name);

        return $group;
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface[] $definitions
     */
    private function createAttributeDefinitionList(
        array $definitions,
        int $totalCount
    ): AttributeDefinitionListInterface {
        $list = $this->createMock(AttributeDefinitionListInterface::class);
        $list->method('getTotalCount')->willReturn($totalCount);
        $list->method('getAttributeDefinitions')->willReturn($definitions);
        $list->method('getIterator')->willReturn(new ArrayIterator($definitions));

        return $list;
    }

    private function createAttributeDefinition(
        string $identifier,
        ?string $name = null,
        ?AttributeTypeInterface $type = null
    ): AttributeDefinitionInterface {
        $definition = $this->createMock(AttributeDefinitionInterface::class);
        $definition->method('getIdentifier')->willReturn($identifier);

        if ($name !== null) {
            $definition->method('getName')->willReturn($name);
        }

        if ($type !== null) {
            $definition->method('getType')->willReturn($type);
        }

        return $definition;
    }

    private function createAttributeType(string $identifier): AttributeTypeInterface
    {
        $type = $this->createMock(AttributeTypeInterface::class);
        $type->method('getIdentifier')->willReturn($identifier);

        return $type;
    }

    /**
     * @param string[] $identifiers
     */
    private function createProductTypeWithAttributes(array $identifiers): ProductTypeInterface
    {
        $assignments = [];
        foreach ($identifiers as $identifier) {
            $assignment = $this->createMock(AttributeDefinitionAssignmentInterface::class);
            $assignment->method('getAttributeDefinition')->willReturn($this->createAttributeDefinition($identifier));

            $assignments[] = $assignment;
        }

        $productType = $this->createMock(ProductTypeInterface::class);
        $productType->method('getAttributesDefinitions')->willReturn($assignments);

        return $productType;
    }

    private function configureExampleData(): void
    {
        $attributeGroupList = $this->createAttributeGroupList([
            $this->createAttributeGroup('group_a', 'Group A'),
            $this->createAttributeGroup('group_b', 'Group B'),
            $this->createAttributeGroup('group_c', 'Group C'),
        ], 3);

        $this->attributeGroupService
            ->method('findAttributeGroups')
            ->with(self::isInstanceOf(AttributeGroupQuery::class))
            ->willReturn($attributeGroupList);

        $this->attributeDefinitionService
            ->method('findAttributesDefinitions')
            ->willReturnOnConsecutiveCalls(
                $this->createAttributeDefinitionList([
                    $this->createAttributeDefinition('foo', 'Foo', $this->createAttributeType('string')),
                    $this->createAttributeDefinition('bar', 'Bar', $this->createAttributeType('integer')),
                    $this->createAttributeDefinition('baz', 'Baz', $this->createAttributeType('boolean')),
                ], 3),
                $this->createAttributeDefinitionList([], 0),
                $this->createAttributeDefinitionList([
                    $this->createAttributeDefinition('foobar', 'Foobar', $this->createAttributeType('string')),
                ], 3),
            );
    }
}
